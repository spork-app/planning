Spork.setupStore({
    Planning: require("./store").default,
})

Spork.component('add-task', require('./Planning/AddTaskForm').default);
Spork.routesFor('planning', [
    Spork.authenticatedRoute('/planning', require('./Planning/KanbanBoard').default),
]);