<script>
    /* eslint-disable */
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Avatar, AvatarFallback, AvatarImage } from "@/lib/components/ui/avatar";
    import ArrowLeftIcon from "@tabler/icons-svelte/icons/arrow-left";
    import DownloadIcon from "@tabler/icons-svelte/icons/download";
    import { Link } from "@inertiajs/svelte";
    import { formatDistanceToNow } from "date-fns";
    import { it } from "date-fns/locale";

    let { post, committee } = $props();

    function formatDate(dateString) {
        if (!dateString) return "";
        return new Date(dateString).toLocaleString("it-IT", {
            day: "2-digit",
            month: "long",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }
</script>

{#snippet headerActions()}
    <Link href={`/me/committees/${committee.id}`} class="p-2">
        <ArrowLeftIcon class="size-5" />
    </Link>
{/snippet}

<MemberLayout title={post.title} {headerActions}>
    <div class="space-y-4">
        <Card.Root>
            <Card.Header class="pb-2">
                <div class="flex items-center gap-3 mb-4">
                    <Avatar class="size-10">
                        <AvatarImage src={post.author.avatar_url} />
                        <AvatarFallback>
                            {post.author.name.charAt(0)}
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold">{post.author.name}</span>
                        <span class="text-xs text-muted-foreground">
                            {formatDistanceToNow(new Date(post.created_at), {
                                addSuffix: true,
                                locale: it,
                            })}
                        </span>
                    </div>
                </div>
                <Card.Title class="text-xl">{post.title}</Card.Title>
                <Card.Description>
                    {formatDate(post.created_at)}
                </Card.Description>
            </Card.Header>
            <Card.Content class="space-y-4">
                <!-- Featured Image -->
                {#if post.featured_image_url}
                    <div class="-mx-4 sm:mx-0">
                        <img
                            src={post.featured_image_url}
                            alt="Immagine"
                            class="w-full object-cover sm:rounded-lg"
                        />
                    </div>
                {/if}

                <!-- Content -->
                <div class="prose prose-sm max-w-none">
                    {@html post.content}
                </div>

                <!-- Attachment Download -->
                {#if post.attachment_url}
                    <div class="border-t pt-4">
                        <a
                            href={post.attachment_url}
                            target="_blank"
                            download={post.attachment_name}
                            class="flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-3 text-sm font-medium text-primary-foreground"
                        >
                            <DownloadIcon class="size-5" />
                            Scarica allegato: {post.attachment_name || "Download"}
                        </a>
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>
</MemberLayout>

<style>
    :global(.prose ul) {
        list-style-type: disc;
        padding-left: 1.5rem;
    }
    :global(.prose ol) {
        list-style-type: decimal;
        padding-left: 1.5rem;
    }
    :global(.prose a) {
        color: hsl(var(--primary));
        text-decoration: underline;
    }
    :global(.prose p) {
        margin-bottom: 0.75rem;
    }
</style>
