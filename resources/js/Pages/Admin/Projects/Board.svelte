<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import { untrack } from "svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import { Badge } from "@/lib/components/ui/badge";
    import * as Card from "@/lib/components/ui/card";
    import * as Dialog from "@/lib/components/ui/dialog";

    let { projects } = $props();

    // Local state for optimistic UI (synced via effect below)
    let localProjects = $state(untrack(() => projects));

    let columns = [
        {
            id: "todo",
            title: "Da Fare",
        },
        {
            id: "in_progress",
            title: "In Corso",
        },
        {
            id: "done",
            title: "Completato",
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
                <h1 class="text-3xl font-bold tracking-tight">Progetti</h1>
                <p class="text-muted-foreground text-sm">
                    Organizza i task per gli eventi.
                </p>
            </div>
            <Button
                onclick={() => (isNewProjectOpen = true)}
                aria-label="Nuovo Task"
            >
                Nuovo task
            </Button>
        </div>

        <!-- Kanban Board -->
        <div class="flex-1 overflow-x-auto">
            <div class="flex h-full space-x-4 min-w-[800px]">
                {#each columns as column}
                    <Card.Root
                        class="flex-1 flex flex-col max-w-sm"
                        ondragover={handleDragOver}
                        ondrop={(e) => handleDrop(e, column.id)}
                        role="region"
                        aria-label={column.title}
                    >
                        <Card.Header class="flex-row items-center justify-between space-y-0">
                            <Card.Title class="text-base">{column.title}</Card.Title>
                            <Badge variant="secondary">
                                {getProjectsByStatus(column.id).length}
                            </Badge>
                        </Card.Header>

                        <Card.Content class="flex-1 space-y-3 overflow-y-auto">
                            {#each getProjectsByStatus(column.id) as project (project.id)}
                                <div
                                    class="rounded-md border bg-card p-4 shadow-sm cursor-move hover:bg-accent/30 transition group relative"
                                    draggable="true"
                                    ondragstart={(e) =>
                                        handleDragStart(e, project.id)}
                                    role="listitem"
                                >
                                    <div
                                        class="flex justify-between items-start mb-2"
                                    >
                                        {#if project.priority === "high"}
                                            <Badge variant="destructive">Alta</Badge>
                                        {:else if project.priority === "medium"}
                                            <Badge variant="secondary">Media</Badge>
                                        {:else}
                                            <Badge variant="outline">Bassa</Badge>
                                        {/if}
                                        {#if project.assignee}
                                            <div
                                                class="h-6 w-6 rounded-full bg-secondary text-secondary-foreground text-[10px] flex items-center justify-center"
                                                title={project.assignee.name}
                                            >
                                                {project.assignee.name.charAt(
                                                    0,
                                                )}
                                            </div>
                                        {/if}
                                    </div>
                                    <h4
                                        class="text-sm font-medium mb-2"
                                    >
                                        {project.title}
                                    </h4>
                                    <div
                                        class="flex justify-end opacity-0 group-hover:opacity-100 transition"
                                    >
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="text-destructive hover:text-destructive"
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
                                            Elimina
                                        </Button>
                                    </div>
                                </div>
                            {/each}
                        </Card.Content>
                    </Card.Root>
                {/each}
            </div>
        </div>
    </div>

    <Dialog.Root bind:open={isNewProjectOpen}>
        <Dialog.Content class="max-w-md">
            <Dialog.Header>
                <Dialog.Title>Nuovo task</Dialog.Title>
                <Dialog.Description>Crea una nuova attività.</Dialog.Description>
            </Dialog.Header>

            <div class="mt-4 space-y-3">
                <Input bind:value={newProjectForm.title} placeholder="Titolo" />
                <select
                    bind:value={newProjectForm.priority}
                    class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                >
                    <option value="low">Bassa</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                </select>
            </div>

            <Dialog.Footer class="mt-6">
                <Dialog.Close>
                    {#snippet child({ props })}
                        <Button {...props} variant="outline">Annulla</Button>
                    {/snippet}
                </Dialog.Close>
                <Button onclick={createProject} disabled={processing}>
                    {processing ? "Salvataggio..." : "Crea"}
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>
</AdminLayout>
