<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import { untrack } from "svelte";

    let { projects } = $props();

    // Local state for optimistic UI (synced via effect below)
    let localProjects = $state(untrack(() => projects));

    let columns = [
        {
            id: "todo",
            title: "Da Fare",
            color: "bg-zinc-800/50 border-zinc-700",
        },
        {
            id: "in_progress",
            title: "In Corso",
            color: "bg-blue-900/10 border-blue-900/30",
        },
        {
            id: "done",
            title: "Completato",
            color: "bg-green-900/10 border-green-900/30",
        },
    ];

    function getProjectsByStatus(status) {
        return localProjects.filter((p) => p.status === status);
    }

    let draggingId = $state(null);

    function handleDragStart(e, id) {
        draggingId = id;
        e.dataTransfer.effectAllowed = "move";
        // Hide ghost/add style if needed
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = "move";
    }

    function handleDrop(e, status) {
        e.preventDefault();
        if (!draggingId) return;

        // Optimistic Update
        const index = localProjects.findIndex((p) => p.id === draggingId);
        if (index !== -1 && localProjects[index].status !== status) {
            localProjects[index].status = status;

            // Persist
            router.patch(
                route("projects.update", draggingId),
                {
                    status: status,
                },
                {
                    preserveScroll: true,
                    onError: () => {
                        // Revert on error (could implement sophisticated revert here)
                        alert("Errore aggiornamento stato");
                        router.reload();
                    },
                },
            );
        }
        draggingId = null;
    }

    // New Project Dialog Logic
    let isNewProjectOpen = $state(false);
    let newProjectForm = $state({
        title: "",
        status: "todo",
        priority: "medium",
    });
    let processing = $state(false);

    function createProject() {
        processing = true;
        router.post(route("projects.store"), newProjectForm, {
            onSuccess: () => {
                isNewProjectOpen = false;
                newProjectForm = {
                    title: "",
                    status: "todo",
                    priority: "medium",
                };
                // Update local state from props is automatic if we rely on props,
                // but since we cloned to localProjects, we should sync or reload.
                // Inertia reload updates props, we need to watch props or just reset localProjects in $effect.
                localProjects = projects; // This might need a reactive sync
            },
            onFinish: () => (processing = false),
        });
    }

    // Sync local state when props change (e.g. after backend save)
    $effect(() => {
        localProjects = projects;
    });
</script>

<AdminLayout title="Progetti">
    <div class="h-full flex flex-col space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">
                    Progetti
                </h1>
                <p class="text-muted-foreground text-sm">
                    Organizza i task per gli eventi.
                </p>
            </div>
            <button
                onclick={() => (isNewProjectOpen = true)}
                class="bg-white text-black px-4 py-2 rounded-md hover:bg-gray-200 transition text-sm font-medium"
                aria-label="Nuovo Task"
            >
                Nuovo Task +
            </button>
        </div>

        <!-- Kanban Board -->
        <div class="flex-1 overflow-x-auto">
            <div class="flex h-full space-x-4 min-w-[800px]">
                {#each columns as column}
                    <div
                        class="flex-1 flex flex-col rounded-lg border {column.color} p-4 max-w-sm"
                        ondragover={handleDragOver}
                        ondrop={(e) => handleDrop(e, column.id)}
                        role="region"
                        aria-label={column.title}
                    >
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-zinc-200">
                                {column.title}
                            </h3>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-400"
                            >
                                {getProjectsByStatus(column.id).length}
                            </span>
                        </div>

                        <div class="flex-1 space-y-3 overflow-y-auto">
                            {#each getProjectsByStatus(column.id) as project (project.id)}
                                <div
                                    class="bg-zinc-900 border border-zinc-800 p-4 rounded-md shadow-sm cursor-move hover:border-zinc-700 transition group relative"
                                    draggable="true"
                                    ondragstart={(e) =>
                                        handleDragStart(e, project.id)}
                                    role="listitem"
                                >
                                    <div
                                        class="flex justify-between items-start mb-2"
                                    >
                                        <span
                                            class="text-xs font-medium px-2 py-0.5 rounded
                                            {project.priority === 'high'
                                                ? 'bg-red-500/10 text-red-500'
                                                : ''}
                                            {project.priority === 'medium'
                                                ? 'bg-yellow-500/10 text-yellow-500'
                                                : ''}
                                            {project.priority === 'low'
                                                ? 'bg-blue-500/10 text-blue-500'
                                                : ''}
                                        "
                                        >
                                            {project.priority
                                                .charAt(0)
                                                .toUpperCase() +
                                                project.priority.slice(1)}
                                        </span>
                                        {#if project.assignee}
                                            <div
                                                class="h-6 w-6 rounded-full bg-zinc-800 text-[10px] flex items-center justify-center text-zinc-300"
                                                title={project.assignee.name}
                                            >
                                                {project.assignee.name.charAt(
                                                    0,
                                                )}
                                            </div>
                                        {/if}
                                    </div>
                                    <h4
                                        class="text-sm font-medium text-white mb-2"
                                    >
                                        {project.title}
                                    </h4>
                                    <div
                                        class="flex justify-end opacity-0 group-hover:opacity-100 transition"
                                    >
                                        <button
                                            class="text-zinc-500 hover:text-red-400"
                                            aria-label="Elimina Task"
                                            onclick={() => {
                                                if (confirm("Eliminare?"))
                                                    router.delete(
                                                        route(
                                                            "projects.destroy",
                                                            project.id,
                                                        ),
                                                    );
                                            }}
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="14"
                                                height="14"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-trash-2"
                                                ><path d="M3 6h18" /><path
                                                    d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"
                                                /><path
                                                    d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"
                                                /><line
                                                    x1="10"
                                                    x2="10"
                                                    y1="11"
                                                    y2="17"
                                                /><line
                                                    x1="14"
                                                    x2="14"
                                                    y1="11"
                                                    y2="17"
                                                /></svg
                                            >
                                        </button>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </div>

    <!-- New Project Dialog -->
    {#if isNewProjectOpen}
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        >
            <div
                class="bg-zinc-900 border border-zinc-800 p-6 rounded-lg shadow-xl w-full max-w-md space-y-4"
            >
                <h2 class="text-xl font-bold text-white">Nuovo Task</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block">
                            <span class="text-xs text-zinc-400 mb-1"
                                >Titolo</span
                            >
                            <input
                                bind:value={newProjectForm.title}
                                type="text"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                            />
                        </label>
                    </div>
                    <div>
                        <label class="block">
                            <span class="text-xs text-zinc-400 mb-1"
                                >Priorità</span
                            >
                            <select
                                bind:value={newProjectForm.priority}
                                class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                            >
                                <option value="low">Bassa</option>
                                <option value="medium">Media</option>
                                <option value="high">Alta</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button
                        onclick={() => (isNewProjectOpen = false)}
                        class="px-4 py-2 text-sm text-zinc-400 hover:text-white"
                        >Annulla</button
                    >
                    <button
                        onclick={createProject}
                        disabled={processing}
                        class="px-4 py-2 text-sm bg-white text-black font-medium rounded hover:bg-gray-200 disabled:opacity-50"
                    >
                        {processing ? "Salvataggio..." : "Crea Task"}
                    </button>
                </div>
            </div>
        </div>
    {/if}
</AdminLayout>
