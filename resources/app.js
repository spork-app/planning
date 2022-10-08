Spork.setupStore({
    Planning: require("./store").default,
})

Spork.component('add-task', require('./Planning/AddTaskForm').default);
Spork.routesFor('planning', [
    Spork.authenticatedRoute('/planning', require('./Planning/Planning').default, {
        children: [
            Spork.authenticatedRoute('tasks', require('./Planning/Tasks').default),
            Spork.authenticatedRoute('kanban', require('./Planning/KanbanBoard').default),
        ]
    }),
]);