<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import ChevronLeft from "lucide-svelte/icons/chevron-left";
    import ChevronRight from "lucide-svelte/icons/chevron-right";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import { Badge } from "@/lib/components/ui/badge";
    import * as Card from "@/lib/components/ui/card";
    import * as Dialog from "@/lib/components/ui/dialog";

    let { events, currentDate } = $props();

    // State for Date Navigation
    let current = $derived(new Date(currentDate));
    let headerTitle = $derived.by(() =>
        `Eventi · ${current.toLocaleString("it-IT", { month: "long", year: "numeric" })}`,
    );

    // Calendar Generation Logic
    let days = $derived.by(() => {
        const year = current.getFullYear();
        const month = current.getMonth();

        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);

        const startDay =
            firstDayOfMonth.getDay() === 0 ? 6 : firstDayOfMonth.getDay() - 1; // Adjust for Monday start
        const endDay = lastDayOfMonth.getDate();

        const totalCells = Math.ceil((startDay + endDay) / 7) * 7;

        let d = [];
        for (let i = 0; i < totalCells; i++) {
            if (i < startDay || i >= startDay + endDay) {
                d.push(null);
            } else {
                d.push(new Date(year, month, i - startDay + 1));
            }
        }
        return d;
    });

    function previousMonth() {
        const d = new Date(current);
        d.setMonth(d.getMonth() - 1);
        visit(d);
    }

    function nextMonth() {
        const d = new Date(current);
        d.setMonth(d.getMonth() + 1);
        visit(d);
    }

    function visit(date) {
        let year = date.getFullYear();
        let month = date.getMonth() + 1;
        router.get(
            "/admin/events",
            { year, month },
            { preserveState: false },
        );
    }

    function getEventsForDay(date) {
        if (!date) return [];
        const dateStr = date.toISOString().split("T")[0];
        // Simple logic: return events that encompass this day
        return events.filter((e) => {
            const start = e.start_date.split("T")[0];
            const end = e.end_date.split("T")[0];
            return dateStr >= start && dateStr <= end;
        });
    }

    // Dialog State
    let isNewEventOpen = $state(false);
    let isEditEventOpen = $state(false);
    let isDialogOpen = $state(false);
    let selectedEvent = $state(null);
    let loading = $state(false);

    let eventForm = $state({
        id: null,
        title: "",
        start_date: "",
        end_date: "",
        type: "fair",
    });

    function openNewEvent(date) {
        if (!date) return;
        const dateStr = date.toISOString().split("T")[0];
        eventForm = {
            id: null,
            title: "",
            start_date: dateStr, // Default to clicked date (needs time handling in real app)
            end_date: dateStr,
            type: "fair",
        };
        isNewEventOpen = true;
        isEditEventOpen = false;
        isDialogOpen = true;
    }

    function openEditEvent(event, e) {
        e.stopPropagation();
        selectedEvent = event;
        eventForm = {
            id: event.id,
            title: event.title,
            start_date: event.start_date.split("T")[0], // Simplified for date input
            end_date: event.end_date.split("T")[0],
            type: event.type,
        };
        isEditEventOpen = true;
        isNewEventOpen = false;
        isDialogOpen = true;
    }

    function closeDialogs() {
        isNewEventOpen = false;
        isEditEventOpen = false;
        isDialogOpen = false;
        selectedEvent = null;
    }

    // If the dialog is closed via overlay/escape, keep flags in sync.
    $effect(() => {
        if (!isDialogOpen && (isNewEventOpen || isEditEventOpen)) {
            closeDialogs();
        }
    });

    function submitEvent() {
        loading = true;
        const data = {
            ...eventForm,
            // Add default times for simplicity as inputs are date only for now
            start_date: eventForm.start_date + " 09:00:00",
            end_date: eventForm.end_date + " 23:59:59",
        };

        if (eventForm.id) {
            router.put(`/admin/events/${eventForm.id}`, data, {
                onSuccess: closeDialogs,
                onFinish: () => (loading = false),
            });
        } else {
            router.post("/admin/events", data, {
                onSuccess: closeDialogs,
                onFinish: () => (loading = false),
            });
        }
    }

    function deleteEvent() {
        if (!selectedEvent) return;
        if (!confirm("Sei sicuro di voler eliminare questo evento?")) return;

        loading = true;
        router.delete(`/admin/events/${selectedEvent.id}`, {
            onSuccess: closeDialogs,
            onFinish: () => (loading = false),
        });
    }
</script>

<AdminLayout title={headerTitle}>
    {#snippet headerActions()}
                <Button
                    variant="outline"
                    size="icon"
                    onclick={previousMonth}
                    aria-label="Mese precedente"
                >
                    <ChevronLeft />
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    onclick={nextMonth}
                    aria-label="Mese successivo"
                >
                    <ChevronRight />
                </Button>
            <Button onclick={() => openNewEvent(new Date())}>Nuovo evento</Button>
    {/snippet}

    <div class="h-full flex flex-col space-y-4">
        <!-- Calendar Grid -->
        <div
            class="grid grid-cols-7 gap-px bg-border border border-border rounded-lg overflow-hidden flex-1"
        >
            <!-- Weekday Headers -->
            {#each ["Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom"] as day}
                <div
                    class="bg-card py-2 text-center text-xs font-semibold text-muted-foreground"
                >
                    {day}
                </div>
            {/each}

            <!-- Days -->
            {#each days as date}
                <div
                    class="bg-background min-h-[120px] p-2 relative group hover:bg-accent/40 transition cursor-pointer"
                    onclick={() => openNewEvent(date)}
                    role="button"
                    tabindex="0"
                    onkeydown={(e) => e.key === "Enter" && openNewEvent(date)}
                >
                    {#if date}
                        <span
                            class="text-sm font-medium {date.toDateString() ===
                            new Date().toDateString()
                                ? 'bg-primary text-primary-foreground rounded-full w-6 h-6 flex items-center justify-center'
                                : 'text-muted-foreground'}"
                        >
                            {date.getDate()}
                        </span>

                        <!-- Events List -->
                        <div class="mt-2 space-y-1">
                            {#each getEventsForDay(date) as event}
                                <div
                                    class="truncate cursor-pointer select-none"
                                    onclick={(e) => openEditEvent(event, e)}
                                    role="button"
                                    tabindex="0"
                                    onkeydown={(e) =>
                                        e.key === "Enter" &&
                                        openEditEvent(event, e)}
                                >
                                    {#if event.type === "fair"}
                                        <Badge>{event.title}</Badge>
                                    {:else if event.type === "festival"}
                                        <Badge variant="secondary">{event.title}</Badge>
                                    {:else}
                                        <Badge variant="outline">{event.title}</Badge>
                                    {/if}
                                </div>
                            {/each}
                        </div>
                    {/if}
                </div>
            {/each}
        </div>
    </div>

    <!-- Dialog (Crea/Modifica) -->
    <Dialog.Root
        bind:open={isDialogOpen}
    >
        <Dialog.Content class="max-w-md">
            <Dialog.Header>
                <Dialog.Title>
                    {isEditEventOpen ? "Modifica evento" : "Nuovo evento"}
                </Dialog.Title>
                <Dialog.Description>Inserisci i dettagli base.</Dialog.Description>
            </Dialog.Header>

            <div class="mt-4 space-y-3">
                <Input bind:value={eventForm.title} placeholder="Titolo" />
                <div class="grid grid-cols-2 gap-2">
                    <Input bind:value={eventForm.start_date} type="date" />
                    <Input bind:value={eventForm.end_date} type="date" />
                </div>
                <select
                    bind:value={eventForm.type}
                    class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                >
                    <option value="fair">Fiera</option>
                    <option value="festival">Sagra/Festival</option>
                    <option value="meeting">Riunione</option>
                </select>
            </div>

            <Dialog.Footer class="mt-6 justify-between sm:justify-between">
                {#if isEditEventOpen}
                    <Button
                        variant="destructive"
                        onclick={deleteEvent}
                        disabled={loading}
                    >
                        Elimina
                    </Button>
                {:else}
                    <div></div>
                {/if}

                <div class="flex gap-2">
                    {#if isEditEventOpen && selectedEvent}
                        <Button
                            variant="outline"
                            href={`/admin/events/${selectedEvent.id}/checkins`}
                        >
                            Check-in
                        </Button>
                    {/if}
                    <Dialog.Close>
                        {#snippet child({ props })}
                            <Button {...props} variant="outline" disabled={loading}
                                >Annulla</Button
                            >
                        {/snippet}
                    </Dialog.Close>
                    <Button onclick={submitEvent} disabled={loading}>
                        {loading ? "Salvataggio..." : "Salva"}
                    </Button>
                </div>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>
</AdminLayout>
