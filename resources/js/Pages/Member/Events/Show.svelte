<script>
    /* eslint-disable */
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Badge } from "@/lib/components/ui/badge";
    import ArrowLeftIcon from "@tabler/icons-svelte/icons/arrow-left";
    import { Link } from "@inertiajs/svelte";

    let { event } = $props();

    function fmtDateTime(iso) {
        if (!iso) return "";
        const d = new Date(iso);
        if (Number.isNaN(d.getTime())) return "";
        return d.toLocaleString("it-IT", {
            weekday: "long",
            day: "2-digit",
            month: "long",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    function typeLabel(t) {
        if (t === "fair") return "Fiera";
        if (t === "festival") return "Sagra";
        if (t === "meeting") return "Riunione";
        return t || "-";
    }
</script>

{#snippet headerActions()}
    <Link href="/me/events" class="p-2">
        <ArrowLeftIcon class="size-5" />
    </Link>
{/snippet}

<MemberLayout title={event.title} {headerActions}>
    <div class="space-y-4">
        <Card.Root>
            <Card.Header>
                <div class="flex items-center justify-between mb-2">
                    <Badge variant={event.type === "festival" ? "secondary" : event.type === "meeting" ? "outline" : "default"}>
                        {typeLabel(event.type)}
                    </Badge>
                </div>
                <Card.Title class="text-2xl">{event.title}</Card.Title>
                <Card.Description class="space-y-1">
                    <div class="flex flex-col">
                        <span class="font-medium text-foreground">Inizio:</span>
                        <span>{fmtDateTime(event.start_date)}</span>
                    </div>
                    <div class="flex flex-col pt-1">
                        <span class="font-medium text-foreground">Fine:</span>
                        <span>{fmtDateTime(event.end_date)}</span>
                    </div>
                </Card.Description>
            </Card.Header>
            <Card.Content>
                <div class="prose prose-sm max-w-none">
                    {#if event.description}
                        <div class="whitespace-pre-wrap text-sm text-foreground/80 leading-relaxed">
                            {event.description}
                        </div>
                    {:else}
                        <p class="text-muted-foreground italic">Nessuna descrizione disponibile.</p>
                    {/if}
                </div>
            </Card.Content>
        </Card.Root>
    </div>
</MemberLayout>
