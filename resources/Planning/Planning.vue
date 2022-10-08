<template>
    <dual-menu-panel
        name="Planning"
        :navigation="routes"
        redirect="/planning"
        store-query-action="queryPlanning"
    />
</template>

<script>
    import DualMenuPanel from "@core/components/DualMenuPanel";
    import {ViewBoardsIcon, CheckCircleIcon,ServerIcon, DocumentAddIcon, CogIcon, LinkIcon } from "@heroicons/vue/outline";

    export default {
        name: "Planning",
        components: {
            DualMenuPanel,
        },

        computed: {
            routes() {
                return [
                    {
                        name: 'Kanban',
                        href: '/planning/kanban',
                        icon: ViewBoardsIcon,
                        current: false,
                    },
                    {
                        name: 'Task List',
                        href: '/planning/tasks',
                        icon: CheckCircleIcon,
                        current: false,
                    },
                ]
            }
        },
        mounted() {
        }
    }

</script>

<style scoped>

</style>
