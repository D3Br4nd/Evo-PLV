<script>
    import AdminLayout from "../../layouts/AdminLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import { router } from "@inertiajs/svelte";
    let { stats } = $props();
</script>

<AdminLayout title="Dashboard">
    <div class="space-y-6">
        {#snippet headerActions()}
            <Button variant="outline" onclick={() => router.get("/admin/members")}>
                Vai ai soci
            </Button>
        {/snippet}

        <p class="text-sm text-muted-foreground">
                Benvenuto nel pannello amministrativo Pro Loco Venticanese
            </p>

        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-border bg-card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Soci Totali
                    </h3>
                </div>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-foreground">{stats?.membersTotal ?? 0}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        Attivi ({new Date().getFullYear()}): {stats?.membersActive ?? 0}
                    </p>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Eventi
                    </h3>
                </div>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-foreground">{stats?.eventsTotal ?? 0}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        In programma: {stats?.eventsUpcoming ?? 0}
                    </p>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Task
                    </h3>
                </div>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-foreground">{stats?.projectsTotal ?? 0}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        Completati: {stats?.projectsDone ?? 0}
                    </p>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Contenuti
                    </h3>
                </div>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-foreground">—</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        (in arrivo)
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-card p-6">
            <h2 class="text-lg font-semibold text-foreground">Attività recente</h2>
            <p class="mt-2 text-sm text-muted-foreground">
                Nessuna attività da mostrare (sezione in arrivo).
            </p>
        </div>
    </div>
</AdminLayout>
