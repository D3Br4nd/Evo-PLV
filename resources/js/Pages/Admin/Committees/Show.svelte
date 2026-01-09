<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import * as Dialog from "@/lib/components/ui/dialog";
    import * as Select from "@/lib/components/ui/select";
    import * as Popover from "@/lib/components/ui/popover";
    import * as Command from "@/lib/components/ui/command";
    import { cn } from "@/lib/utils";
    import * as Table from "@/lib/components/ui/table";
    import { Input } from "@/lib/components/ui/input";
    import { Label } from "@/lib/components/ui/label";
    import { Textarea } from "@/lib/components/ui/textarea";
    import { Badge } from "@/lib/components/ui/badge";
    import { router } from "@inertiajs/svelte";
    import PlusIcon from "@tabler/icons-svelte/icons/plus";
    import TrashIcon from "@tabler/icons-svelte/icons/trash";
    import UserIcon from "@tabler/icons-svelte/icons/user";
    import CheckIcon from "@tabler/icons-svelte/icons/check";
    import SelectorIcon from "@tabler/icons-svelte/icons/selector";
    import { formatDistanceToNow } from "date-fns";
    import { it } from "date-fns/locale";

    let { committee, availableMembers = [] } = $props();

    let addMemberDialogOpen = $state(false);
    let createPostDialogOpen = $state(false);
    let openCombobox = $state(false);

    let selectedMemberId = $state(null);
    let memberRole = $state("");

    let postFormData = $state({
        title: "",
        content: "",
    });

    function handleAddMember() {
        if (!selectedMemberId) return;

        router.post(
            `/admin/committees/${committee.id}/members`,
            {
                user_id: selectedMemberId,
                role: memberRole || null,
            },
            {
                onSuccess: () => {
                    addMemberDialogOpen = false;
                    selectedMemberId = null;
                    memberRole = "";
                },
            },
        );
    }

    function handleRemoveMember(userId) {
        if (
            confirm("Sei sicuro di voler rimuovere questo membro dal comitato?")
        ) {
            router.delete(
                `/admin/committees/${committee.id}/members/${userId}`,
            );
        }
    }

    function handleCreatePost() {
        router.post(`/admin/committees/${committee.id}/posts`, postFormData, {
            onSuccess: () => {
                createPostDialogOpen = false;
                postFormData = { title: "", content: "" };
            },
        });
    }

    function handleDeletePost(postId) {
        if (confirm("Sei sicuro di voler eliminare questo post?")) {
            router.delete(`/admin/committees/${committee.id}/posts/${postId}`);
        }
    }
</script>

<AdminLayout title={committee.name}>
    <div class="space-y-6">
        <!-- Committee Header -->
        <div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {committee.name}
                    </h1>
                    {#if committee.description}
                        <p class="mt-2 text-muted-foreground">
                            {committee.description}
                        </p>
                    {/if}
                </div>
                <Badge
                    variant={committee.status === "active"
                        ? "default"
                        : "secondary"}
                >
                    {committee.status === "active" ? "Attivo" : "Inattivo"}
                </Badge>
            </div>
        </div>

        <!-- Members Section -->
        <Card.Root>
            <Card.Header>
                <div class="flex items-center justify-between">
                    <div>
                        <Card.Title>Membri del Comitato</Card.Title>
                        <Card.Description>
                            {committee.members.length}
                            {committee.members.length === 1
                                ? "membro"
                                : "membri"} associati
                        </Card.Description>
                    </div>
                    <Button
                        onclick={() => (addMemberDialogOpen = true)}
                        disabled={availableMembers.length === 0}
                    >
                        <PlusIcon class="mr-2 size-4" />
                        Aggiungi Socio
                    </Button>
                </div>
            </Card.Header>
            <Card.Content>
                {#if committee.members.length === 0}
                    <div
                        class="flex flex-col items-center justify-center py-8 text-center"
                    >
                        <UserIcon class="mb-3 size-12 text-muted-foreground" />
                        <p class="mb-2 text-sm font-medium">Nessun membro</p>
                        <p class="text-sm text-muted-foreground">
                            Aggiungi dei membri a questo comitato.
                        </p>
                    </div>
                {:else}
                    <Table.Root>
                        <Table.Header>
                            <Table.Row>
                                <Table.Head>Nome</Table.Head>
                                <Table.Head>Email</Table.Head>
                                <Table.Head>Ruolo</Table.Head>
                                <Table.Head>Aggiunto il</Table.Head>
                                <Table.Head class="w-12"></Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each committee.members as member}
                                <Table.Row>
                                    <Table.Cell class="font-medium"
                                        >{member.name}</Table.Cell
                                    >
                                    <Table.Cell>{member.email}</Table.Cell>
                                    <Table.Cell>
                                        {member.pivot.role || "-"}
                                    </Table.Cell>
                                    <Table.Cell class="text-muted-foreground">
                                        {new Date(
                                            member.pivot.joined_at,
                                        ).toLocaleDateString("it-IT")}
                                    </Table.Cell>
                                    <Table.Cell>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onclick={() =>
                                                handleRemoveMember(member.id)}
                                        >
                                            <TrashIcon
                                                class="size-4 text-destructive"
                                            />
                                        </Button>
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        </Table.Body>
                    </Table.Root>
                {/if}
            </Card.Content>
        </Card.Root>

        <!-- Posts Section (Bulletin Board) -->
        <Card.Root>
            <Card.Header>
                <div class="flex items-center justify-between">
                    <div>
                        <Card.Title>Bacheca</Card.Title>
                        <Card.Description>
                            Post e comunicazioni del comitato
                        </Card.Description>
                    </div>
                    <Button onclick={() => (createPostDialogOpen = true)}>
                        <PlusIcon class="mr-2 size-4" />
                        Nuovo Post
                    </Button>
                </div>
            </Card.Header>
            <Card.Content>
                {#if committee.posts.length === 0}
                    <div
                        class="flex flex-col items-center justify-center py-8 text-center"
                    >
                        <p class="mb-2 text-sm font-medium">Nessun post</p>
                        <p class="text-sm text-muted-foreground">
                            Pubblica il primo post nella bacheca del comitato.
                        </p>
                    </div>
                {:else}
                    <div class="space-y-4">
                        {#each committee.posts as post}
                            <div class="rounded-lg border p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold">
                                            {post.title}
                                        </h4>
                                        <div
                                            class="mt-1 flex items-center gap-2 text-sm text-muted-foreground"
                                        >
                                            <span>{post.author.name}</span>
                                            <span>•</span>
                                            <span>
                                                {formatDistanceToNow(
                                                    new Date(post.created_at),
                                                    {
                                                        addSuffix: true,
                                                        locale: it,
                                                    },
                                                )}
                                            </span>
                                        </div>
                                        <p
                                            class="mt-3 whitespace-pre-wrap text-sm"
                                        >
                                            {post.content}
                                        </p>
                                    </div>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        onclick={() =>
                                            handleDeletePost(post.id)}
                                    >
                                        <TrashIcon
                                            class="size-4 text-destructive"
                                        />
                                    </Button>
                                </div>
                            </div>
                        {/each}
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>

    <!-- Add Member Dialog -->
    <Dialog.Root bind:open={addMemberDialogOpen}>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>Aggiungi Socio al Comitato</Dialog.Title>
                <Dialog.Description>
                    Seleziona un socio da aggiungere al comitato.
                </Dialog.Description>
            </Dialog.Header>

            <form
                onsubmit={(e) => {
                    e.preventDefault();
                    handleAddMember();
                }}
                class="space-y-4"
            >
                <div class="space-y-2">
                    <Label>Socio <span class="text-red-500">*</span></Label>
                    <div class="relative">
                        <Popover.Root bind:open={openCombobox}>
                            <Popover.Trigger>
                                {#snippet child({ props })}
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        {...props}
                                        class="w-full justify-between"
                                    >
                                        {selectedMemberId
                                            ? availableMembers.find(
                                                  (m) =>
                                                      m.id === selectedMemberId,
                                              )?.name
                                            : "Seleziona socio..."}
                                        <SelectorIcon
                                            class="ml-2 size-4 shrink-0 opacity-50"
                                        />
                                    </Button>
                                {/snippet}
                            </Popover.Trigger>
                            <Popover.Content
                                class="w-[--bits-popover-anchor-width] p-0"
                            >
                                <Command.Root>
                                    <Command.Input
                                        placeholder="Cerca socio..."
                                    />
                                    <Command.List>
                                        <Command.Empty
                                            >Nessun socio trovato.</Command.Empty
                                        >
                                        <Command.Group>
                                            {#each availableMembers as member}
                                                <Command.Item
                                                    value={member.name}
                                                    onSelect={() => {
                                                        selectedMemberId =
                                                            member.id;
                                                        openCombobox = false;
                                                    }}
                                                >
                                                    <CheckIcon
                                                        class={cn(
                                                            "mr-2 size-4",
                                                            selectedMemberId ===
                                                                member.id
                                                                ? "opacity-100"
                                                                : "opacity-0",
                                                        )}
                                                    />
                                                    {member.name}
                                                    {#if member.email}
                                                        <span
                                                            class="ml-2 text-xs text-muted-foreground"
                                                        >
                                                            {member.email}
                                                        </span>
                                                    {/if}
                                                </Command.Item>
                                            {/each}
                                        </Command.Group>
                                    </Command.List>
                                </Command.Root>
                            </Popover.Content>
                        </Popover.Root>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="role">Ruolo (opzionale)</Label>
                    <Input
                        id="role"
                        type="text"
                        bind:value={memberRole}
                        placeholder="es. Coordinatore, Membro, Segretario..."
                    />
                </div>

                <Dialog.Footer>
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => (addMemberDialogOpen = false)}
                    >
                        Annulla
                    </Button>
                    <Button type="submit" disabled={!selectedMemberId}
                        >Aggiungi</Button
                    >
                </Dialog.Footer>
            </form>
        </Dialog.Content>
    </Dialog.Root>

    <!-- Create Post Dialog -->
    <Dialog.Root bind:open={createPostDialogOpen}>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>Nuovo Post</Dialog.Title>
                <Dialog.Description>
                    Pubblica un nuovo post nella bacheca del comitato.
                </Dialog.Description>
            </Dialog.Header>

            <form
                onsubmit={(e) => {
                    e.preventDefault();
                    handleCreatePost();
                }}
                class="space-y-4"
            >
                <div class="space-y-2">
                    <Label for="title"
                        >Titolo <span class="text-red-500">*</span></Label
                    >
                    <Input
                        id="title"
                        type="text"
                        bind:value={postFormData.title}
                        placeholder="Titolo del post..."
                        required
                    />
                </div>

                <div class="space-y-2">
                    <Label for="content"
                        >Contenuto <span class="text-red-500">*</span></Label
                    >
                    <Textarea
                        id="content"
                        bind:value={postFormData.content}
                        placeholder="Scrivi il contenuto del post..."
                        rows={6}
                        required
                    />
                </div>

                <Dialog.Footer>
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => (createPostDialogOpen = false)}
                    >
                        Annulla
                    </Button>
                    <Button type="submit">Pubblica</Button>
                </Dialog.Footer>
            </form>
        </Dialog.Content>
    </Dialog.Root>
</AdminLayout>
