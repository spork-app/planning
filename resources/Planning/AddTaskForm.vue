<template>
    <form
        class="relative flex flex-col justify-between bg-white dark:bg-gray-500 rounded-md shadow overflow-hidden"
        @submit.prevent="handleAddNewTask"
    >
        <div class="p-3 flex-1">
            <input
                class="dark:bg-gray-600 placeholder-gray-300 block w-full px-2 py-1 text-lg rounded"
                type="text"
                placeholder="Enter a title"
                v-model.trim="newTask.title"
            />
            <textarea
                class="dark:bg-gray-600 placeholder-gray-300 mt-3 p-2 block w-full p-1 text-sm rounded"
                rows="2"
                placeholder="Add a description (optional)"
                v-model.trim="newTask.description"
            ></textarea>
            <div v-show="errorMessage">
                <span class="text-xs text-red-500">
                  {{ errorMessage }}
                </span>
            </div>
        </div>
        <div class="p-3 flex justify-between items-end text-sm bg-blue-100 dark:bg-gray-700">
            <button
                @click="$emit('task-canceled')"
                type="reset"
                class="py-1 leading-5 text-gray-600 dark:text-gray-200 hover:text-gray-700 dark:hover:text-gray-400"
            >
                Cancel
            </button>
            <button
                :disabled="creating"
                type="submit"
                class="px-3 py-1 leading-5 text-white bg-green-500 hover:bg-orange-500 rounded"
                :class="creating ? 'opacity-75' : ''"
            >
                Add<span v-if="creating">ing</span>
            </button>
        </div>
    </form>
</template>

<script>
import { ref } from 'vue';
export default {
    props: {
        statusId: Number
    },
    setup() {
        return {
            newTask: ref({
                title: "",
                description: "",
                status_id: null,
                order: 0,
            }),
            creating: ref(false),
            errorMessage: ref("")
        };
    },
    mounted() {
        this.newTask.status_id = this.statusId;
    },
    methods: {
        handleAddNewTask() {
            // Basic validation so we don't send an empty task to the server
            if (!this.newTask.title) {
                this.errorMessage = "The title field is required";
                return;
            }
            this.creating = true;
            // Send new task to server
            axios
                .post("/api/planning/tasks", this.newTask)
                .then(res => {
                    // Tell the parent component we've added a new task and include it
                    this.$emit("task-added", res.data);
                    this.creating = false;
                })
                .catch(err => {
                    // Handle the error returned from our request
                    this.handleErrors(err);
                    this.creating = false;
                });
        },
        handleErrors(err) {
            if (err.response && err.response.status === 422) {
                // We have a validation error
                const errorBag = err.response.data.errors;
                if (errorBag.title) {
                    this.errorMessage = errorBag.title[0];
                } else if (errorBag.description) {
                    this.errorMessage = errorBag.description[0];
                } else {
                    this.errorMessage = err.response.message;
                }
            } else {
                // We have bigger problems
                console.log(err.response);
            }
        }
    }
};
</script>
