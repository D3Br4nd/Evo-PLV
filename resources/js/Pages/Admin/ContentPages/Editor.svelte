<script>
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import * as Dialog from "@/lib/components/ui/dialog";
    import { Input } from "@/lib/components/ui/input";
    import { Label } from "@/lib/components/ui/label";
    import { Textarea } from "@/lib/components/ui/textarea";
    import { router } from "@inertiajs/svelte";
    import { toast } from "svelte-sonner";
    import { onMount, onDestroy } from "svelte";
    import { Editor } from "@tiptap/core";
    import StarterKit from "@tiptap/starter-kit";
    import Link from "@tiptap/extension-link";
    import BoldIcon from "@tabler/icons-svelte/icons/bold";
    import ItalicIcon from "@tabler/icons-svelte/icons/italic";
    import ListIcon from "@tabler/icons-svelte/icons/list";
    import ListNumbersIcon from "@tabler/icons-svelte/icons/list-numbers";
    import LinkIcon from "@tabler/icons-svelte/icons/link";
    import SendIcon from "@tabler/icons-svelte/icons/send";

    let { initialData = null, isEditing = false } = $props();

    let form = $state({
        title: initialData?.title || "",
        slug: initialData?.slug || "",
        excerpt: initialData?.excerpt || "",
        body: initialData?.body || "",
        status: initialData?.status || "draft",
    });

    let editorElement = $state(null);
    let editor = $state(null);
    let processing = $state(false);

    // Link dialog state
    let linkDialogOpen = $state(false);
    let linkUrl = $state("");

    onMount(() => {
        editor = new Editor({
            element: editorElement,
            extensions: [
                StarterKit,
                Link.configure({
                    openOnClick: false,
                    HTMLAttributes: {
                        class: "text-primary underline",
                    },
                }),
            ],
            content: form.body,
            editorProps: {
                attributes: {
                    class: "prose prose-sm max-w-none min-h-[300px] p-4 focus:outline-none",
                },
            },
            onTransaction: () => {
                // Force reactivity
                editor = editor;
            },
        });
    });

    onDestroy(() => {
        if (editor) {
            editor.destroy();
        }
    });

    function openLinkDialog() {
        const previousUrl = editor?.getAttributes("link").href || "";
        linkUrl = previousUrl;
        linkDialogOpen = true;
    }

    function applyLink() {
        if (linkUrl === "") {
            editor?.chain().focus().extendMarkRange("link").unsetLink().run();
        } else {
            editor
                ?.chain()
                .focus()
                .extendMarkRange("link")
                .setLink({ href: linkUrl })
                .run();
        }
        linkDialogOpen = false;
        linkUrl = "";
    }

    function removeLink() {
        editor?.chain().focus().extendMarkRange("link").unsetLink().run();
        linkDialogOpen = false;
        linkUrl = "";
    }

    function submit() {
        if (!form.title.trim()) {
            toast.error("Inserisci un titolo.");
            return;
        }

        form.body = editor?.getHTML() || "";
        if (!form.body || form.body === "<p></p>") {
            toast.error("Inserisci un contenuto.");
            return;
        }

        processing = true;

        if (isEditing) {
            router.put(`/admin/content-pages/${initialData.id}`, form, {
                onFinish: () => (processing = false),
                onError: (errors) => {
                    const firstError = Object.values(errors)[0];
                    if (firstError) toast.error(firstError);
                },
            });
        } else {
            router.post("/admin/content-pages", form, {
                onFinish: () => (processing = false),
                onError: (errors) => {
                    const firstError = Object.values(errors)[0];
                    if (firstError) toast.error(firstError);
                },
            });
        }
    }
</script>

<div class="space-y-6">
    <Card.Root>
        <Card.Content class="space-y-6 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="space-y-2">
                    <Label for="title"
                        >Titolo <span class="text-red-500">*</span></Label
                    >
                    <Input
                        id="title"
                        type="text"
                        bind:value={form.title}
                        placeholder="Titolo della pagina..."
                    />
                </div>

                <!-- Slug -->
                <div class="space-y-2">
                    <Label for="slug">Slug (opzionale)</Label>
                    <Input
                        id="slug"
                        type="text"
                        bind:value={form.slug}
                        placeholder="lascia vuoto per auto-generare"
                        class="font-mono text-sm"
                    />
                </div>
            </div>

            <!-- Excerpt -->
            <div class="space-y-2">
                <Label for="excerpt">Excerpt / Riassunto breve</Label>
                <Textarea
                    id="excerpt"
                    bind:value={form.excerpt}
                    placeholder="Un breve riassunto per i motori di ricerca o anteprime..."
                    rows={2}
                />
            </div>

            <!-- Rich Text Editor -->
            <div class="space-y-2">
                <Label>Contenuto <span class="text-red-500">*</span></Label>
                <div class="rounded-lg border">
                    <!-- Toolbar -->
                    <div class="flex flex-wrap gap-1 border-b bg-muted/30 p-2">
                        <Button
                            type="button"
                            variant={editor?.isActive("bold")
                                ? "secondary"
                                : "ghost"}
                            size="icon"
                            class="size-8"
                            onclick={() =>
                                editor?.chain().focus().toggleBold().run()}
                            disabled={!editor}
                        >
                            <BoldIcon class="size-4" />
                        </Button>
                        <Button
                            type="button"
                            variant={editor?.isActive("italic")
                                ? "secondary"
                                : "ghost"}
                            size="icon"
                            class="size-8"
                            onclick={() =>
                                editor?.chain().focus().toggleItalic().run()}
                            disabled={!editor}
                        >
                            <ItalicIcon class="size-4" />
                        </Button>
                        <div class="mx-1 w-px bg-border"></div>
                        <Button
                            type="button"
                            variant={editor?.isActive("bulletList")
                                ? "secondary"
                                : "ghost"}
                            size="icon"
                            class="size-8"
                            onclick={() =>
                                editor
                                    ?.chain()
                                    .focus()
                                    .toggleBulletList()
                                    .run()}
                            disabled={!editor}
                        >
                            <ListIcon class="size-4" />
                        </Button>
                        <Button
                            type="button"
                            variant={editor?.isActive("orderedList")
                                ? "secondary"
                                : "ghost"}
                            size="icon"
                            class="size-8"
                            onclick={() =>
                                editor
                                    ?.chain()
                                    .focus()
                                    .toggleOrderedList()
                                    .run()}
                            disabled={!editor}
                        >
                            <ListNumbersIcon class="size-4" />
                        </Button>
                        <div class="mx-1 w-px bg-border"></div>
                        <Button
                            type="button"
                            variant={editor?.isActive("link")
                                ? "secondary"
                                : "ghost"}
                            size="icon"
                            class="size-8"
                            onclick={openLinkDialog}
                            disabled={!editor}
                        >
                            <LinkIcon class="size-4" />
                        </Button>
                    </div>
                    <!-- Editor -->
                    <div bind:this={editorElement}></div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 pt-4 border-t">
                <div class="flex items-center gap-3">
                    <Label for="status">Stato</Label>
                    <select
                        id="status"
                        bind:value={form.status}
                        class="h-10 rounded-md border border-input bg-background px-3 text-sm"
                    >
                        <option value="draft">Bozza</option>
                        <option value="published">Pubblicata</option>
                    </select>
                </div>

                <Button onclick={submit} disabled={processing}>
                    <SendIcon class="mr-2 size-4" />
                    {processing ? "Salvataggio..." : "Salva Pagina"}
                </Button>
            </div>
        </Card.Content>
    </Card.Root>
</div>

<!-- Link Dialog -->
<Dialog.Root bind:open={linkDialogOpen}>
    <Dialog.Content class="sm:max-w-md">
        <Dialog.Header>
            <Dialog.Title>Inserisci Link</Dialog.Title>
            <Dialog.Description>
                Inserisci l'URL del link da applicare al testo selezionato.
            </Dialog.Description>
        </Dialog.Header>
        <div class="space-y-4 py-4">
            <div class="space-y-2">
                <Label for="linkUrl">URL</Label>
                <Input
                    id="linkUrl"
                    type="url"
                    bind:value={linkUrl}
                    placeholder="https://esempio.com"
                />
            </div>
        </div>
        <Dialog.Footer class="flex gap-2">
            {#if editor?.isActive("link")}
                <Button variant="destructive" onclick={removeLink}>
                    Rimuovi Link
                </Button>
            {/if}
            <Button variant="outline" onclick={() => (linkDialogOpen = false)}>
                Annulla
            </Button>
            <Button onclick={applyLink}>Applica</Button>
        </Dialog.Footer>
    </Dialog.Content>
</Dialog.Root>

<style>
    :global(.ProseMirror) {
        min-height: 300px;
    }
    :global(.ProseMirror p.is-editor-empty:first-child::before) {
        color: #adb5bd;
        content: attr(data-placeholder);
        float: left;
        height: 0;
        pointer-events: none;
    }
    :global(.ProseMirror ul) {
        list-style-type: disc;
        padding-left: 1.5rem;
    }
    :global(.ProseMirror ol) {
        list-style-type: decimal;
        padding-left: 1.5rem;
    }
    :global(.ProseMirror a) {
        color: hsl(var(--primary));
        text-decoration: underline;
    }
</style>
