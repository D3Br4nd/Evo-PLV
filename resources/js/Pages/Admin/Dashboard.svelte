<script>
    import AdminLayout from "../../layouts/AdminLayout.svelte";
</script>

<AdminLayout>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="border-b border-border pb-4">
            <h1 class="text-3xl font-bold text-foreground">Dashboard</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Benvenuto nel pannello amministrativo Pro Loco Venticanese
            </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-border bg-card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Soci Totali
                    </h3>
                </div>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-foreground">248</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        +12 questo mese
                    </p>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Eventi Attivi
                    </h3>
                </div>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-foreground">8</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        3 in programma
                    </p>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Progetti
                    </h3>
                </div>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-foreground">15</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        5 completati
                    </p>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Volontari
                    </h3>
                </div>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-foreground">42</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        Attivi oggi
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="rounded-lg border border-border bg-card">
            <div class="border-b border-border p-6">
                <h2 class="text-lg font-semibold text-foreground">
                    Attività Recente
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-medium"
                        >
                            MR
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-foreground">
                                Nuovo socio registrato
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Mario Rossi si è iscritto alla Pro Loco
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                2 ore fa
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary text-secondary-foreground text-xs font-medium"
                        >
                            EV
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-foreground">
                                Evento aggiornato
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Fiera Campionaria 2025 - Data modificata
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                5 ore fa
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-accent text-accent-foreground text-xs font-medium"
                        >
                            PR
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-foreground">
                                Progetto completato
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Restauro fontana del centro storico
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                1 giorno fa
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AdminLayout>
