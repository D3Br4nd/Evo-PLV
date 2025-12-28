<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import { onMount } from "svelte";

    let { events, currentDate } = $props();

    // State for Date Navigation
    let current = $derived(new Date(currentDate));

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
            route("events.index"),
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
    }

    function closeDialogs() {
        isNewEventOpen = false;
        isEditEventOpen = false;
        selectedEvent = null;
    }

    function submitEvent() {
        loading = true;
        const data = {
            ...eventForm,
            // Add default times for simplicity as inputs are date only for now
            start_date: eventForm.start_date + " 09:00:00",
            end_date: eventForm.end_date + " 23:59:59",
        };

        if (eventForm.id) {
            router.put(route("events.update", eventForm.id), data, {
                onSuccess: closeDialogs,
                onFinish: () => (loading = false),
            });
        } else {
            router.post(route("events.store"), data, {
                onSuccess: closeDialogs,
                onFinish: () => (loading = false),
            });
        }
    }

    function deleteEvent() {
        if (!selectedEvent) return;
        if (!confirm("Sei sicuro di voler eliminare questo evento?")) return;

        loading = true;
        router.delete(route("events.destroy", selectedEvent.id), {
            onSuccess: closeDialogs,
            onFinish: () => (loading = false),
        });
    }
</script>

<AdminLayout title="Eventi">
    <div class="h-full flex flex-col space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1
                class="text-3xl font-bold tracking-tight text-white flex items-center space-x-4"
            >
                <button
                    onclick={previousMonth}
                    class="p-1 hover:bg-zinc-800 rounded"
                    aria-label="Mese precedente"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-chevron-left"
                        ><path d="m15 18-6-6 6-6" /></svg
                    >
                </button>
                <span class="capitalize"
                    >{current.toLocaleString("it-IT", {
                        month: "long",
                        year: "numeric",
                    })}</span
                >
                <button
                    onclick={nextMonth}
                    class="p-1 hover:bg-zinc-800 rounded"
                    aria-label="Mese successivo"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-chevron-right"
                        ><path d="m9 18 6-6-6-6" /></svg
                    >
                </button>
            </h1>
            <button
                onclick={() => openNewEvent(new Date())}
                class="bg-white text-black px-4 py-2 rounded-md hover:bg-gray-200 transition text-sm font-medium"
            >
                Nuovo Evento +
            </button>
        </div>

        <!-- Calendar Grid -->
        <div
            class="grid grid-cols-7 gap-px bg-zinc-800 border border-zinc-800 rounded-lg overflow-hidden flex-1"
        >
            <!-- Weekday Headers -->
            {#each ["Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom"] as day}
                <div
                    class="bg-zinc-900 py-2 text-center text-xs font-semibold text-zinc-400"
                >
                    {day}
                </div>
            {/each}

            <!-- Days -->
            {#each days as date}
                <div
                    class="bg-zinc-950 min-h-[120px] p-2 relative group hover:bg-zinc-900/50 transition cursor-pointer"
                    onclick={() => openNewEvent(date)}
                    role="button"
                    tabindex="0"
                    onkeydown={(e) => e.key === "Enter" && openNewEvent(date)}
                >
                    {#if date}
                        <span
                            class="text-sm font-medium {date.toDateString() ===
                            new Date().toDateString()
                                ? 'bg-white text-black rounded-full w-6 h-6 flex items-center justify-center'
                                : 'text-zinc-400'}"
                        >
                            {date.getDate()}
                        </span>

                        <!-- Events List -->
                        <div class="mt-2 space-y-1">
                            {#each getEventsForDay(date) as event}
                                <div
                                    class="text-xs px-2 py-1 rounded truncate cursor-pointer transition select-none
                                    {event.type === 'fair'
                                        ? 'bg-white text-black font-bold'
                                        : ''}
                                    {event.type === 'festival'
                                        ? 'bg-zinc-800 text-white border border-zinc-700'
                                        : ''}
                                    {event.type === 'meeting'
                                        ? 'border border-zinc-600 text-zinc-400'
                                        : ''}
                                    "
                                    onclick={(e) => openEditEvent(event, e)}
                                    role="button"
                                    tabindex="0"
                                    onkeydown={(e) =>
                                        e.key === "Enter" &&
                                        openEditEvent(event, e)}
                                >
                                    {event.title}
                                </div>
                            {/each}
                        </div>
                    {/if}
                </div>
            {/each}
        </div>
    </div>

    <!-- Dialog (Merged Create/Edit) -->
    {#if isNewEventOpen || isEditEventOpen}
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        >
            <div
                class="bg-zinc-900 border border-zinc-800 p-6 rounded-lg shadow-xl w-full max-w-md space-y-4"
            >
                <h2 class="text-xl font-bold text-white">
                    {isEditEventOpen ? "Modifica Evento" : "Nuovo Evento"}
                </h2>

                <div class="space-y-3">
                    <div>
                        <label class="block">
                            <span class="text-xs text-zinc-400 mb-1"
                                >Titolo</span
                            >
                            <input
                                bind:value={eventForm.title}
                                type="text"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                            />
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block">
                                <span class="text-xs text-zinc-400 mb-1"
                                    >Inizio</span
                                >
                                <input
                                    bind:value={eventForm.start_date}
                                    type="date"
                                    class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                                />
                            </label>
                        </div>
                        <div>
                            <label class="block">
                                <span class="text-xs text-zinc-400 mb-1"
                                    >Fine</span
                                >
                                <input
                                    bind:value={eventForm.end_date}
                                    type="date"
                                    class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                                />
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block">
                            <span class="text-xs text-zinc-400 mb-1">Tipo</span>
                            <select
                                bind:value={eventForm.type}
                                class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                            >
                                <option value="fair">Fiera</option>
                                <option value="festival">Sagra/Festival</option>
                                <option value="meeting">Riunione</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    {#if isEditEventOpen}
                        <button
                            onclick={deleteEvent}
                            type="button"
                            class="text-red-500 text-sm hover:underline"
                            >Elimina</button
                        >
                    {:else}
                        <div></div>
                    {/if}
                    <div class="flex space-x-2">
                        <button
                            onclick={closeDialogs}
                            class="px-4 py-2 text-sm text-zinc-400 hover:text-white"
                            >Annulla</button
                        >
                        <button
                            onclick={submitEvent}
                            disabled={loading}
                            class="px-4 py-2 text-sm bg-white text-black font-medium rounded hover:bg-gray-200 disabled:opacity-50"
                        >
                            {loading ? "Salvataggio..." : "Salva"}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</AdminLayout>
