<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import { page } from "@inertiajs/svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import { Textarea } from "@/lib/components/ui/textarea";
    import { Badge } from "@/lib/components/ui/badge";
    import * as Card from "@/lib/components/ui/card";
    import * as Table from "@/lib/components/ui/table";

    let { pages } = $props();
    let flash = $derived($page.props.flash);

    let processing = $state(false);
    let editing = $state(null);

    let form = $state({
        title: "",
        slug: "",
        excerpt: "",
        body: "",
        status: "draft",
    });

    function reset() {
        form = { title: "", slug: "", excerpt: "", body: "", status: "draft" };
        editing = null;
    }

    function edit(page) {
        editing = page;
        form = {
            title: page.title,
            slug: page.slug,
            excerpt: page.excerpt || "",
            body: page.body,
            status: page.status,
        };
    }

    function submit() {
        processing = true;
        if (editing) {
            router.put(`/admin/content-pages/${editing.id}`, form, {
                preserveScroll: true,
                onFinish: () => (processing = false),
            });
        } else {
            router.post("/admin/content-pages", form, {
                preserveScroll: true,
                onSuccess: reset,
                onFinish: () => (processing = false),
            });
        }
    }

    function remove(pageId) {
        if (!confirm("Eliminare questa pagina?")) return;
        router.delete(`/admin/content-pages/${pageId}`, {
            preserveScroll: true,
        });
    }
</script>

<AdminLayout title="Contenuti">
    {#snippet headerActions()}
        {#if editing}
            <Button variant="outline" onclick={reset}>Nuova pagina</Button>
        {/if}
    {/snippet}

    <div class="space-y-6">
        <p class="text-sm text-muted-foreground">
                    Pagine pubbliche pubblicabili su <code>/p/{'{'}slug{'}'}</code>.
                </p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card.Root>
                <Card.Header>
                    <Card.Title>{editing ? "Modifica" : "Crea"} pagina</Card.Title>
                    <Card.Description>
                        Gestisci contenuti pubblici e bozze.
                    </Card.Description>
                </Card.Header>
                <Card.Content class="space-y-3">
                    <div class="space-y-1.5">
                        <label for="cp-title" class="text-xs text-muted-foreground"
                            >Titolo</label
                        >
                        <Input id="cp-title" bind:value={form.title} />
                        {#if $page.props.errors?.title}
                            <p class="text-sm text-destructive">{$page.props.errors.title}</p>
                        {/if}
                    </div>
                    <div class="space-y-1.5">
                        <label for="cp-slug" class="text-xs text-muted-foreground"
                            >Slug</label
                        >
                        <Input
                            id="cp-slug"
                            bind:value={form.slug}
                            placeholder="lascia vuoto per auto"
                            class="font-mono text-xs"
                        />
                        {#if $page.props.errors?.slug}
                            <p class="text-sm text-destructive">{$page.props.errors.slug}</p>
                        {/if}
                    </div>
                    <div class="space-y-1.5">
                        <label
                            for="cp-excerpt"
                            class="text-xs text-muted-foreground"
                            >Excerpt</label
                        >
                        <Input id="cp-excerpt" bind:value={form.excerpt} />
                        {#if $page.props.errors?.excerpt}
                            <p class="text-sm text-destructive">{$page.props.errors.excerpt}</p>
                        {/if}
                    </div>
                    <div class="space-y-1.5">
                        <label for="cp-body" class="text-xs text-muted-foreground"
                            >Body</label
                        >
                        <Textarea id="cp-body" bind:value={form.body} rows={10} />
                        {#if $page.props.errors?.body}
                            <p class="text-sm text-destructive">{$page.props.errors.body}</p>
                        {/if}
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <select
                            bind:value={form.status}
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="draft">Bozza</option>
                            <option value="published">Pubblicata</option>
                        </select>
                        <Button onclick={submit} disabled={processing}>
                            {processing ? "Salvataggio..." : "Salva"}
                        </Button>
                    </div>

                    {#if flash?.error}
                        <div class="text-sm text-destructive">{flash.error}</div>
                    {/if}
                    {#if flash?.success}
                        <div class="text-sm text-green-600 dark:text-green-400">
                            {flash.success}
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>

            <Card.Root>
                <Card.Header>
                    <Card.Title>Pagine</Card.Title>
                    <Card.Description>Elenco contenuti e stato.</Card.Description>
                </Card.Header>
                <Card.Content class="p-0">
                    <Table.Root>
                        <Table.Header class="bg-muted">
                            <Table.Row>
                                <Table.Head>Titolo</Table.Head>
                                <Table.Head>Slug</Table.Head>
                                <Table.Head>Stato</Table.Head>
                                <Table.Head class="text-right">Azioni</Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each pages.data as p (p.id)}
                                <Table.Row>
                                    <Table.Cell class="font-medium">
                                        <div class="space-y-1">
                                            <div class="truncate">{p.title}</div>
                                            {#if p.excerpt}
                                                <div class="text-xs text-muted-foreground truncate">
                                                    {p.excerpt}
                                                </div>
                                            {/if}
                                        </div>
                                    </Table.Cell>
                                    <Table.Cell class="font-mono text-xs text-muted-foreground">
                                        /p/{p.slug}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {#if p.status === "published"}
                                            <Badge>Pubblicata</Badge>
                                        {:else}
                                            <Badge variant="secondary">Bozza</Badge>
                                        {/if}
                                    </Table.Cell>
                                    <Table.Cell class="text-right">
                                        <div class="flex justify-end gap-2">
                                        <Button variant="outline" size="sm" onclick={() => edit(p)}>
                                            Modifica
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            onclick={() => remove(p.id)}
                                        >
                                            Elimina
                                        </Button>
                                        </div>
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        </Table.Body>
                    </Table.Root>
                </Card.Content>
            </Card.Root>
        </div>
    </div>
</AdminLayout>


