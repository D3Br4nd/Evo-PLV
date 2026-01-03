<script>
    import { router, inertia } from "@inertiajs/svelte";
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { page } from "@inertiajs/svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import { Badge } from "@/lib/components/ui/badge";
    import * as Card from "@/lib/components/ui/card";
    import * as Table from "@/lib/components/ui/table";
    import * as Dialog from "@/lib/components/ui/dialog";

    let { users, flash } = $props();
    let canManageRoles = $derived($page.props.auth?.can?.manageRoles);

    let search = $state("");
    let isNewMemberOpen = $state(false);
    let isDeleteMemberOpen = $state(false);
    let deleteConfirmationUserId = $state(null);
    let processing = $state(false);

    // Keep local search input in sync with server-provided filters (Inertia reactive props).
    $effect(() => {
        search = $page.props?.filters?.search || "";
    });

    // Debounce search
    let timer;
    function handleSearch(e) {
        clearTimeout(timer);
        timer = setTimeout(() => {
            router.get(
                route("members.index"),
                { search: search },
                { preserveState: true, replace: true },
            );
        }, 300);
    }

    function openNewMemberModal() {
        isNewMemberOpen = true;
    }

    function closeNewMemberModal() {
        isNewMemberOpen = false;
    }

    function openDeleteModal(userId) {
        deleteConfirmationUserId = userId;
        isDeleteMemberOpen = true;
    }

    function closeDeleteModal() {
        deleteConfirmationUserId = null;
        isDeleteMemberOpen = false;
    }

    // If the dialog is closed via overlay/escape, keep the selected user id in sync.
    $effect(() => {
        if (!isDeleteMemberOpen && deleteConfirmationUserId) {
            closeDeleteModal();
        }
    });

    function deleteMember() {
        if (!deleteConfirmationUserId) return;

        processing = true;
        router.delete(route("members.destroy", deleteConfirmationUserId), {
            onFinish: () => {
                processing = false;
                closeDeleteModal();
            },
        });
    }

    // Form Handling for New Member would go here or in a separate component.
    // For now, I'll implement a basic form inside the dialog.
    let newMemberForm = $state({
        name: "",
        email: "",
        role: "member",
    });

    function createMember() {
        processing = true;
        router.post(route("members.store"), newMemberForm, {
            onSuccess: () => {
                closeNewMemberModal();
                newMemberForm = { name: "", email: "", role: "member" };
            },
            onFinish: () => {
                processing = false;
            },
        });
    }

    function updateUserRole(userId, role) {
        router.patch(
            route("members.role.update", userId),
            { role },
            { preserveScroll: true, preserveState: true },
        );
    }
</script>

<AdminLayout title="Soci">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Soci</h1>
                <p class="text-muted-foreground text-sm">
                    Gestisci i membri dell'associazione.
                </p>
            </div>
            <Button onclick={openNewMemberModal}>Nuovo socio</Button>
        </div>

        <!-- Search -->
        <div class="flex items-center space-x-2">
            <Input
                type="text"
                bind:value={search}
                oninput={handleSearch}
                placeholder="Cerca soci..."
                class="w-full max-w-sm"
            />
        </div>

        <!-- Table -->
        <Card.Root>
            <Card.Content class="p-0">
                <Table.Root>
                    <Table.Header>
                        <Table.Row>
                            <Table.Head>Nome</Table.Head>
                            <Table.Head>UUID</Table.Head>
                            <Table.Head>Status</Table.Head>
                            <Table.Head>Ruolo</Table.Head>
                            <Table.Head class="text-right">Azioni</Table.Head>
                        </Table.Row>
                    </Table.Header>
                    <Table.Body>
                        {#each users.data as user (user.id)}
                            <Table.Row>
                                <Table.Cell class="font-medium">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary text-secondary-foreground text-xs font-semibold"
                                        >
                                            {user.name.charAt(0).toUpperCase()}
                                        </div>
                                        <span class="truncate">{user.name}</span>
                                    </div>
                                </Table.Cell>
                                <Table.Cell class="font-mono text-xs text-muted-foreground">
                                    {user.id.substring(0, 8)}…
                                </Table.Cell>
                                <Table.Cell>
                                    {#if user.memberships && user.memberships.length > 0}
                                        <Badge variant="secondary">Attivo</Badge>
                                    {:else}
                                        <Badge variant="outline">Scaduto</Badge>
                                    {/if}
                                </Table.Cell>
                                <Table.Cell class="capitalize">
                                    {#if canManageRoles}
                                        <select
                                            value={user.role}
                                            onchange={(e) =>
                                                updateUserRole(user.id, e.currentTarget.value)}
                                            class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm"
                                        >
                                            <option value="super_admin">SuperAdmin</option>
                                            <option value="direzione">Direzione</option>
                                            <option value="segreteria">Segreteria</option>
                                            <option value="member">Socio</option>
                                        </select>
                                    {:else}
                                        <span class="text-muted-foreground">{user.role}</span>
                                    {/if}
                                </Table.Cell>
                                <Table.Cell class="text-right">
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        onclick={() => openDeleteModal(user.id)}
                                    >
                                        Elimina
                                    </Button>
                                </Table.Cell>
                            </Table.Row>
                        {/each}
                    </Table.Body>
                </Table.Root>
            </Card.Content>

            <!-- Pagination -->
            <div
                class="px-6 py-4 border-t border-border flex items-center justify-between"
            >
                <div class="text-xs text-muted-foreground">
                    Membri {users.from || 0} - {users.to || 0} di {users.total}
                </div>
                <div class="space-x-1">
                    {#each users.links as link}
                        {#if link.url}
                            <a
                                href={link.url}
                                class="px-3 py-1 text-xs rounded-md border border-border hover:bg-accent transition {link.active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-muted-foreground'}"
                                use:inertia
                            >
                                {@html link.label}
                            </a>
                        {:else}
                            <span
                                class="px-3 py-1 text-xs text-muted-foreground border border-border/50 rounded-md"
                            >
                                {@html link.label}
                            </span>
                        {/if}
                    {/each}
                </div>
            </div>
        </Card.Root>
    </div>

    <Dialog.Root bind:open={isNewMemberOpen}>
        <Dialog.Content class="max-w-md">
            <Dialog.Header>
                <Dialog.Title>Nuovo socio</Dialog.Title>
                <Dialog.Description>Crea un nuovo membro.</Dialog.Description>
            </Dialog.Header>

            <div class="mt-4 space-y-3">
                <Input bind:value={newMemberForm.name} placeholder="Nome" />
                <Input
                    bind:value={newMemberForm.email}
                    type="email"
                    placeholder="Email"
                />
                {#if canManageRoles}
                    <select
                        bind:value={newMemberForm.role}
                        class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                    >
                        <option value="member">Socio</option>
                        <option value="segreteria">Segreteria</option>
                        <option value="direzione">Direzione</option>
                        <option value="super_admin">SuperAdmin</option>
                    </select>
                {:else}
                    <Input value="Socio" disabled />
                {/if}
            </div>

            <Dialog.Footer class="mt-6">
                <Dialog.Close>
                    {#snippet child({ props })}
                        <Button {...props} variant="outline">Annulla</Button>
                    {/snippet}
                </Dialog.Close>
                <Button onclick={createMember} disabled={processing}>
                    {processing ? "Salvataggio..." : "Crea"}
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>

    <Dialog.Root bind:open={isDeleteMemberOpen}>
        <Dialog.Content class="max-w-md">
            <Dialog.Header>
                <Dialog.Title>Elimina socio</Dialog.Title>
                <Dialog.Description>
                    Questa azione non può essere annullata.
                </Dialog.Description>
            </Dialog.Header>

            <Dialog.Footer class="mt-6">
                <Dialog.Close>
                    {#snippet child({ props })}
                        <Button {...props} variant="outline">Annulla</Button>
                    {/snippet}
                </Dialog.Close>
                <Button variant="destructive" onclick={deleteMember} disabled={processing}>
                    {processing ? "Eliminazione..." : "Elimina"}
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>
</AdminLayout>
