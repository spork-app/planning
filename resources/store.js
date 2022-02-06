export default {
    data: {
        assigningUser: false,
        overlayOpen: false,
        statuses: [],
        assignableUsers: [],
        loading: true,
    },
    getters: {
        statuses: state => state.statuses,
        tasksLoading: state => state.loading,
        assignableUsers: state => state.assignableUsers,
    },
    actions: {
        async moveTask({ state }) {
            await axios.put("/api/planning/sync", { columns: state.statuses })
        },
        async updateTaskOrder({ state, dispatch }, newTask) {
            // Find the index of the status where we should add the task
            const statusIndex = state.statuses.findIndex(
                status => status.id === newTask.status_id
            );
            // Add newly created task to our column
            state.statuses[statusIndex].tasks.push(newTask);
            // Reset and close the AddTaskForm
            await dispatch('fetchTasks')

        },
        async fetchTasks({ commit, state }, payload) {
            state.loading = true;
            const { data } = await axios.get('/api/planning/status?include=tasks.creator,tasks.assignee')
            
            state.statuses = data.sort((a, b) => a.order > b.order ? 1 : -1);
            state.loading = false;
        },
        async assignUserToTask({ state, dispatch }, { id, user}) {
            state.assigningUser = true;
            await axios.post('/api/planning/assign-task', {
                task_id: id,
                user_id: user.id,
            })
            state.assigningUser = false;
            await dispatch('fetchTasks')
        },
        async removeUserFromTask({ state, dispatch }, { id, user }) {
            state.assigningUser = true;
            await axios.post('/api/planning/remove-task', {
                task_id: id,
                user_id: user.id,
            })
            state.assigningUser = false;
            await dispatch('fetchTasks')
        },
        async fetchAssignableUsers({ commit, state }, payload) {
            const { data } = await axios.get('/api/planning/users')
            state.assignableUsers = data;
            console.log('users', data)
        },
        async deleteTask({ state, dispatch }, {id}) {
            await axios.delete(`/api/planning/tasks/${id}`)
            await dispatch('fetchTasks')
        }
    }
}