<script>
    import AdminLayout from "../../layouts/AdminLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import { router } from "@inertiajs/svelte";
    import * as Card from "@/lib/components/ui/card";
    import * as Table from "@/lib/components/ui/table";
    let { stats, activity } = $props();

    function go(url) {
        if (!url) return;
        router.get(url, {}, { preserveScroll: true });
    }
</script>

<AdminLayout title="Dashboard">
    <div class="space-y-6">
        {#snippet headerActions()}
            <Button
                variant="outline"
                onclick={() => router.get("/admin/members")}
            >
                Vai ai soci
            </Button>
        {/snippet}

        <p class="text-sm text-muted-foreground">
            Benvenuto nel pannello amministrativo Pro Loco Venticanese
        </p>

        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card.Root>
                <Card.Header
                    class="flex flex-row items-center justify-between space-y-0 pb-2"
                >
                    <Card.Title class="text-sm font-medium"
                        >Soci Totali</Card.Title
                    >
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">
                        {stats?.membersTotal ?? 0}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Attivi ({new Date().getFullYear()}): {stats?.membersActive ??
                            0}
                    </p>
                </Card.Content>
            </Card.Root>

            <Card.Root>
                <Card.Header
                    class="flex flex-row items-center justify-between space-y-0 pb-2"
                >
                    <Card.Title class="text-sm font-medium">Eventi</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">
                        {stats?.eventsTotal ?? 0}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        In programma: {stats?.eventsUpcoming ?? 0}
                    </p>
                </Card.Content>
            </Card.Root>

            <Card.Root>
                <Card.Header
                    class="flex flex-row items-center justify-between space-y-0 pb-2"
                >
                    <Card.Title class="text-sm font-medium">Task</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">
                        {stats?.projectsTotal ?? 0}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Completati: {stats?.projectsDone ?? 0}
                    </p>
                </Card.Content>
            </Card.Root>

            <Card.Root>
                <Card.Header
                    class="flex flex-row items-center justify-between space-y-0 pb-2"
                >
                    <Card.Title class="text-sm font-medium"
                        >Contenuti</Card.Title
                    >
                </Card.Header>
                <Card.Content>
                    <div class="text-2xl font-bold">—</div>
                    <p class="text-xs text-muted-foreground">(in arrivo)</p>
                </Card.Content>
            </Card.Root>
        </div>
        <Card.Root>
            <Card.Header>
                <Card.Title>Attività recenti</Card.Title>
                <Card.Description>
                    Operazioni su soci, eventi, task e contenuti.
                </Card.Description>
            </Card.Header>
            <Card.Content class="p-0">
                <Table.Root>
                    <Table.Header class="bg-muted">
                        <Table.Row>
                            <Table.Head>Quando</Table.Head>
                            <Table.Head>Chi</Table.Head>
                            <Table.Head>Cosa</Table.Head>
                        </Table.Row>
                    </Table.Header>
                    <Table.Body>
                        {#if activity?.data?.length}
                            {#each activity.data as a (a.id)}
                                <Table.Row>
                                    <Table.Cell
                                        class="text-xs text-muted-foreground"
                                    >
                                        {new Date(a.created_at).toLocaleString(
                                            "it-IT",
                                        )}
                                    </Table.Cell>
                                    <Table.Cell class="text-sm">
                                        {a.actor?.name || "Sistema"}
                                    </Table.Cell>
                                    <Table.Cell class="text-sm">
                                        {a.summary}
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        {:else}
                            <Table.Row>
                                <Table.Cell
                                    colspan="3"
                                    class="text-sm text-muted-foreground"
                                >
                                    Nessuna attività da mostrare.
                                </Table.Cell>
                            </Table.Row>
                        {/if}
                    </Table.Body>
                </Table.Root>
            </Card.Content>
            {#if activity?.links?.length}
                <Card.Content
                    class="flex items-center justify-between gap-3 border-t p-4"
                >
                    <div class="text-xs text-muted-foreground">
                        {activity?.meta?.from || 0}-{activity?.meta?.to || 0} di
                        {activity?.meta?.total || 0}
                    </div>
                    <div class="flex items-center gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            disabled={!activity?.prev_page_url}
                            onclick={() => go(activity?.prev_page_url)}
                        >
                            Precedente
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            disabled={!activity?.next_page_url}
                            onclick={() => go(activity?.next_page_url)}
                        >
                            Successiva
                        </Button>
                    </div>
                </Card.Content>
            {/if}
        </Card.Root>
    </div>
</AdminLayout>
