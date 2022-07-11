<template>
    <div v-if="!$store.getters.tasksLoading" class="flex flex-wrap w-full gap-4 container mx-auto sm:px-4">
        <!-- Thank you https://github.com/messerli90/laravel-vue-kanban-tutorial :D -->
        <div class="w-full rounded-lg">
            <div class="text-4xl font-medium text-gray-900 dark:text-gray-50 my-4">
                Planning
            </div>
        </div>

        <div
            v-for="status in $store.getters.statuses"
            :key="status.slug"
            class="flex-1 "
        >
            <div class="rounded-md shadow-md text-gray-600 dark:text-gray-200 bg-gray-200 dark:bg-gray-800 overflow-hidden">
                <div class="p-3 flex justify-between items-baseline">
                    <div class="font-medium">
                        {{ status.title }}
                    </div>
                    <button
                        @click="openAddTaskForm(status.id)"
                        class="border border-blue-500 py-1 px-2 text-sm hover:underline rounded text-blue-500"
                    >
                        Add Task
                    </button>
                </div>
                <div class="p-2 bg-white dark:bg-gray-600">
                    <!-- Tasks -->
                    <draggable
                        class="flex-1 gap-2 flex flex-col list-group"
                        v-model="status.tasks"
                        v-bind="taskDragOptions"
                        @end="newTask => moveTask(newTask)"
                        item-key="title"
                        style="min-height:1rem;"
                    >
                        <template #header>
                            <AddTaskForm
                                v-if="newTaskForStatus === status.id"
                                :status-id="status.id"
                                v-on:task-added="handleTaskAdded"
                                v-on:task-canceled="closeAddTaskForm"
                            />
                        </template>
                        <template #item="{ element: task }">
                            <transition-group
                                tag="div"
                            >
                                <div
                                    :key="task.id"
                                    class="p-4 flex flex-col dark:bg-slate-700 rounded-md border border-dashed border-gray-300 dark:border-slate-500 rounded transform hover:shadow-md cursor-pointer r"
                                    @contextmenu.prevent="(e) => openContextMenu(e, task)"
                                >
                                    <span class="block mb-2 text-xl text-gray-900 dark:text-slate-50">
                                      {{ task.title }}
                                    </span>
                                    <p class="text-gray-700 dark:text-slate-200 ">
                                        {{ task.description }}
                                    </p>
                                    <div class="mt-4 flex justify-between w-full">
                                        <div class="font-bold">
                                            #{{ task.id }}
                                        </div>
                                        <div class="w-6 h-6 overflow-hidden rounded-full flex items-center justify-center" v-if="task.assignee">
                                            <img :src="task.assignee.profile_photo">
                                        </div>
                                    </div>
                                </div>
                                <!-- ./Tasks -->
                            </transition-group>
                        </template>
                    </draggable>
                </div>
            </div>

        </div>
        <div id="div-context-menu" class="absolute z-10 bg-white rounded shadow-lg p-2" style="top: -9999px; left: -9999px;">
            <div class="w-full flex flex-col">
                <!-- Assgin a user to the task -->
                <div class="uppercase text-gray-600 text-xs">assign user</div>
                <div class="flex flex-col text-sm pt-2" v-if="$store.getters.assignableUsers">
                    <button v-for="user in $store.getters.assignableUsers" :key="'user.' + user.id" @click.prevent="() => assignUser(user)">{{ user.name }}</button>

                    <div v-if="$store.getters.assignableUsers.length === 0">No Users</div>
                </div>

                <!-- Delete task -->
                <button class="bg-red-500 text-white rounded mt-2" @click.prevent="deleteTask">Delete</button>
            </div>
        </div>
        <button v-if="display_background" class="fixed w-full h-full bg-gray-800 opacity-25 top-0 left-0 right-0 bottom-0" @click="display_background = false"></button>
        <!-- ./Columns -->
    </div>
    <div v-else class="container mx-auto">
        <div class="p-4 rounded-lg bg-white dark:bg-gray-600 text-lg m-4 shadow">
            Loading...
        </div>
    </div>
</template>

<script>
import draggable from "vuedraggable";
import AddTaskForm from "./AddTaskForm";
import { ref } from 'vue';

export default {
    components: { draggable, AddTaskForm },
    props: {
        initialData: Array
    },
    setup() {
        return {
            newTaskForStatus: ref(0),
            loading: ref(true),
            targetTask: ref(null),
            popoverX: ref(0),
            popoverY: ref(0),
            display_background: ref(false),
        };
    },
    computed: {
        taskDragOptions() {
            return {
                animation: 200,
                group: "task-list",
                dragClass: "status-drag"
            };
        }
    },
    async mounted() {


        await this.$store.dispatch('fetchAssignableUsers');
        await this.$store.dispatch('fetchTasks')
    },
    watch: {
        popoverY() {
            var menu = document.getElementById("div-context-menu");
            menu.style.left = this.popoverX + 'px'
            menu.style.top = this.popoverY + 'px'
            menu.style.display = 'block'
        },
        display_background(newValue, oldValue) {
            if (oldValue) {
                this.popoverX = -99999;
                this.popoverY = -99999;
            }
        }
    },
    methods: {
        // When you right click
        openContextMenu(e, task) {
            e.preventDefault();
            this.popoverX = e.pageX;
            this.popoverY = e.pageY
            this.targetTask = task;
            this.display_background = true;
        },
        openAddTaskForm(statusId) {
            this.newTaskForStatus = statusId;
        },
        closeAddTaskForm() {
            this.newTaskForStatus = 0;
        },
        async moveTask () {
            await this.$store.dispatch('moveTask');
            this.closeAddTaskForm();
        },
        async handleTaskAdded(newTask) {
            await this.$store.dispatch('updateTaskOrder', newTask)
            this.closeAddTaskForm();
        },
        async assignUser(user) {
            await this.$store.dispatch('assignUserToTask', {id: this.targetTask.id, user})
            this.display_background = false;
            this.closeAddTaskForm();
        },
        async deleteTask() {
            await this.$store.dispatch('deleteTask', this.targetTask);
            this.display_background = false;
            this.closeAddTaskForm();
        }
    }
};
</script>

<style scoped>
.status-drag {
    transition: transform 0.5s;
    transition-property: all;
}
.list-group:empty {
    padding:1rem;
    text-align:center;
    border: dashed 1px #83a2e3;
    border-radius: .5rem;
}

.list-group:empty:before {
    content: 'No tasks yet';
    font-style: italic;
}
</style>
