<template>
    <div class="w-full flex flex-wrap items-center">
        <div class="w-1/3 relative bg-white dark:bg-slate-700 rounded-lg m-4 overflow-hidden">
            <div class="flex flex-wrap items-center gap-2 p-8 bg-indigo-300 dark:bg-indigo-600">
                <div class="text-5xl">{{completed.length}}</div>
                <div class="flex flex-col">
                    <div class="text-base font-bold">Tasks</div>
                    <div class="text-xs">/ {{taskList.length}}</div>
                </div>
            </div>
            <button @click="addTask" class="absolute shadow rounded-full p-2 mr-4 bg-indigo-400 dark:bg-indigo-700 right-0" style="top: 6em;">
                <PlusIcon class="w-6 h-6 " />
            </button>
            <div class="py-2">
                <div v-if="taskList.length === 0">
                    <div class="text-center text-gray-500 dark:text-slate-400">
                        <div class="p-2">No tasks</div>
                    </div>
                </div>

                <div v-for="(task, i) in taskList" :key="i + '-task'" class="flex flex-wrap items-center">
                    <button @click="markAsComplete(task)" class="p-2">
                        <svg v-if="task.completed" class="-mx-px w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div v-else class="mx-px w-5 h-5 border-2 border-indigo-600 dark:border-indigo-300 rounded-full"></div>
                    </button>
                    <input class="dark:text-slate-300 dark:bg-slate-600 outline-none" :disabled="task.completed" :class="{ 'line-through text-slate-400': task.completed, 'text-slate-50': !task.completed }" v-model="task.title" />
                    <button @click="removeTask(task)" class="p-2">
                        <TrashIcon class="w-6 h-6 text-red-600 fill-current" />
                    </button>
                </div>
            </div>
        </div>
        <pre>{{ taskList }}</pre>
    </div>
</template>

<script>
import { ViewBoardsIcon, TrashIcon, ServerIcon, DocumentAddIcon, CogIcon, LinkIcon, PlusIcon } from "@heroicons/vue/outline";
export default {
    components: {
        PlusIcon,
        TrashIcon
    },
    data() {
        return {
            tasks: [],
        }
    },
    methods: {
        addTask() {
            this.tasks.push({
                name: 'New Task',
                description: '',
                completed_at: null,
            })
        },
        markAsComplete(task) {
            task.completed_at = task.completed_at === null ? new Date() : null;
        },
        removeTask(task) {
            this.tasks.splice(this.tasks.indexOf(task), 1);
        },
    },
    computed: {
        completed() {
            return this.taskList.filter(task => task.completed_at);
        },
        taskList() {
            return this.$store.getters.statuses?.filter(status => ["To Do", "In Progress"].includes(status.title))
                ?.reduce((tasks, status) => {
                    const statusTasks = status.tasks;
                    delete status.tasks;

                    return tasks.concat(statusTasks?.map(task => {
                        task.status = status; 
                        return task;
                    }))??[]
                }, [])??[];
        }
    }
}
</script>