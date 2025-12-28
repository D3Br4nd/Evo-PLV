<script>
    import { router, inertia } from "@inertiajs/svelte";
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { page } from "@inertiajs/svelte";

    let { users, filters, flash } = $props();

    let search = $state(filters.search || "");
    let isNewMemberOpen = $state(false);
    let deleteConfirmationUserId = $state(null);
    let processing = $state(false);

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
    }

    function closeDeleteModal() {
        deleteConfirmationUserId = null;
    }

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
</script>

<AdminLayout title="Soci">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">
                    Soci
                </h1>
                <p class="text-muted-foreground text-sm">
                    Gestisci i membri dell'associazione.
                </p>
            </div>
            <button
                onclick={openNewMemberModal}
                class="bg-white text-black px-4 py-2 rounded-md hover:bg-gray-200 transition text-sm font-medium"
            >
                Nuovo Socio +
            </button>
        </div>

        <!-- Search -->
        <div class="flex items-center space-x-2">
            <input
                type="text"
                bind:value={search}
                oninput={handleSearch}
                placeholder="Cerca soci..."
                class="bg-zinc-900 border border-zinc-800 text-white rounded-md px-3 py-2 w-full max-w-sm focus:outline-none focus:ring-1 focus:ring-white"
            />
        </div>

        <!-- Table -->
        <div class="rounded-md border border-zinc-800 bg-zinc-900/50">
            <div class="w-full overflow-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="text-xs text-muted-foreground uppercase border-b border-zinc-800"
                    >
                        <tr>
                            <th class="px-6 py-3">Nome</th>
                            <th class="px-6 py-3">UUID</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Ruolo</th>
                            <th class="px-6 py-3 text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        {#each users.data as user (user.id)}
                            <tr class="hover:bg-zinc-900/80 transition">
                                <td
                                    class="px-6 py-4 font-medium text-white flex items-center space-x-3"
                                >
                                    <div
                                        class="h-8 w-8 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-bold text-white"
                                    >
                                        {user.name.charAt(0).toUpperCase()}
                                    </div>
                                    <span>{user.name}</span>
                                </td>
                                <td
                                    class="px-6 py-4 text-zinc-400 font-mono text-xs"
                                >
                                    {user.id.substring(0, 8)}...
                                </td>
                                <td class="px-6 py-4">
                                    {#if user.memberships && user.memberships.length > 0}
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-500/10 text-green-500"
                                        >
                                            Attivo
                                        </span>
                                    {:else}
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-500/10 text-zinc-500"
                                        >
                                            Scaduto
                                        </span>
                                    {/if}
                                </td>
                                <td class="px-6 py-4 text-zinc-300 capitalize">
                                    {user.role}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button
                                        class="text-zinc-400 hover:text-white transition"
                                    >
                                        <!-- Edit Icon -->
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-pencil"
                                            ><path
                                                d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"
                                            /><path d="m15 5 4 4" /></svg
                                        >
                                    </button>
                                    <button
                                        onclick={() => openDeleteModal(user.id)}
                                        class="text-red-500 hover:text-red-400 transition"
                                    >
                                        <!-- Trash Icon -->
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
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
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                class="px-6 py-4 border-t border-zinc-800 flex items-center justify-between"
            >
                <div class="text-xs text-muted-foreground">
                    Menbri {users.from || 0} - {users.to || 0} di {users.total}
                </div>
                <div class="space-x-1">
                    {#each users.links as link}
                        {#if link.url}
                            <a
                                href={link.url}
                                class="px-3 py-1 text-xs rounded-md border border-zinc-800 hover:bg-zinc-800 transition {link.active
                                    ? 'bg-zinc-800 text-white'
                                    : 'text-zinc-400'}"
                                use:inertia
                            >
                                {@html link.label}
                            </a>
                        {:else}
                            <span
                                class="px-3 py-1 text-xs text-zinc-600 border border-zinc-800/50 rounded-md"
                            >
                                {@html link.label}
                            </span>
                        {/if}
                    {/each}
                </div>
            </div>
        </div>
    </div>

    <!-- New Member Dialog -->
    {#if isNewMemberOpen}
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        >
            <div
                class="bg-zinc-900 border border-zinc-800 p-6 rounded-lg shadow-xl w-full max-w-md space-y-4"
            >
                <h2 class="text-xl font-bold text-white">Nuovo Socio</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block">
                            <span class="text-xs text-zinc-400 mb-1">Nome</span>
                            <input
                                bind:value={newMemberForm.name}
                                type="text"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                            />
                        </label>
                    </div>
                    <div>
                        <label class="block">
                            <span class="text-xs text-zinc-400 mb-1">Email</span
                            >
                            <input
                                bind:value={newMemberForm.email}
                                type="email"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                            />
                        </label>
                    </div>
                    <div>
                        <label class="block">
                            <span class="text-xs text-zinc-400 mb-1">Ruolo</span
                            >
                            <select
                                bind:value={newMemberForm.role}
                                class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                            >
                                <option value="member">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button
                        onclick={closeNewMemberModal}
                        class="px-4 py-2 text-sm text-zinc-400 hover:text-white"
                        >Annulla</button
                    >
                    <button
                        onclick={createMember}
                        disabled={processing}
                        class="px-4 py-2 text-sm bg-white text-black font-medium rounded hover:bg-gray-200 disabled:opacity-50"
                    >
                        {processing ? "Salvataggio..." : "Crea Socio"}
                    </button>
                </div>
            </div>
        </div>
    {/if}

    <!-- Delete Confirmation Modal -->
    {#if deleteConfirmationUserId}
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        >
            <div
                class="bg-zinc-900 border border-zinc-800 p-6 rounded-lg shadow-xl w-full max-w-md space-y-4"
            >
                <h2 class="text-xl font-bold text-red-500">Elimina Socio</h2>
                <p class="text-zinc-400 text-sm">
                    Sei sicuro di voler eliminare questo socio? Questa azione
                    non può essere annullata.
                </p>

                <div class="flex justify-end space-x-2 pt-2">
                    <button
                        onclick={closeDeleteModal}
                        class="px-4 py-2 text-sm text-zinc-400 hover:text-white"
                        >Annulla</button
                    >
                    <button
                        onclick={deleteMember}
                        disabled={processing}
                        class="px-4 py-2 text-sm bg-red-600 text-white font-medium rounded hover:bg-red-700 disabled:opacity-50"
                    >
                        {processing ? "Eliminazione..." : "Elimina"}
                    </button>
                </div>
            </div>
        </div>
    {/if}
</AdminLayout>
