<script>
    import { useForm } from "@inertiajs/svelte";
    import { Mail, Lock, User as UserIcon, UserPlus } from "lucide-svelte";

    // Form state
    const form = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });

    function handleSubmit(e) {
        e.preventDefault();
        $form.post("/register");
    }
</script>

<div
    class="min-h-screen flex items-center justify-center bg-background px-4 sm:px-6 lg:px-8"
>
    <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-foreground">
                Pro Loco Venticanese
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                Crea il tuo account
            </p>
        </div>

        <!-- Register Form -->
        <div class="rounded-lg border border-border bg-card p-8 shadow-sm">
            <form onsubmit={handleSubmit} class="space-y-6">
                <!-- Name Field -->
                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium text-foreground"
                    >
                        Nome completo
                    </label>
                    <div class="relative mt-1">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <UserIcon class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <input
                            id="name"
                            type="text"
                            bind:value={$form.name}
                            required
                            class="block w-full rounded-md border border-input bg-background py-2 pl-10 pr-3 text-foreground placeholder:text-muted-foreground focus:border-ring focus:outline-none focus:ring-1 focus:ring-ring"
                            placeholder="Mario Rossi"
                        />
                    </div>
                    {#if $form.errors.name}
                        <p class="mt-1 text-sm text-destructive">
                            {$form.errors.name}
                        </p>
                    {/if}
                </div>

                <!-- Email Field -->
                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-foreground"
                    >
                        Email
                    </label>
                    <div class="relative mt-1">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <Mail class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <input
                            id="email"
                            type="email"
                            bind:value={$form.email}
                            required
                            class="block w-full rounded-md border border-input bg-background py-2 pl-10 pr-3 text-foreground placeholder:text-muted-foreground focus:border-ring focus:outline-none focus:ring-1 focus:ring-ring"
                            placeholder="mario.rossi@example.com"
                        />
                    </div>
                    {#if $form.errors.email}
                        <p class="mt-1 text-sm text-destructive">
                            {$form.errors.email}
                        </p>
                    {/if}
                </div>

                <!-- Password Field -->
                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium text-foreground"
                    >
                        Password
                    </label>
                    <div class="relative mt-1">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <Lock class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <input
                            id="password"
                            type="password"
                            bind:value={$form.password}
                            required
                            class="block w-full rounded-md border border-input bg-background py-2 pl-10 pr-3 text-foreground placeholder:text-muted-foreground focus:border-ring focus:outline-none focus:ring-1 focus:ring-ring"
                            placeholder="••••••••"
                        />
                    </div>
                    {#if $form.errors.password}
                        <p class="mt-1 text-sm text-destructive">
                            {$form.errors.password}
                        </p>
                    {/if}
                </div>

                <!-- Password Confirmation Field -->
                <div>
                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium text-foreground"
                    >
                        Conferma password
                    </label>
                    <div class="relative mt-1">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <Lock class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <input
                            id="password_confirmation"
                            type="password"
                            bind:value={$form.password_confirmation}
                            required
                            class="block w-full rounded-md border border-input bg-background py-2 pl-10 pr-3 text-foreground placeholder:text-muted-foreground focus:border-ring focus:outline-none focus:ring-1 focus:ring-ring"
                            placeholder="••••••••"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    disabled={$form.processing}
                    class="flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50"
                >
                    {#if $form.processing}
                        <div
                            class="h-4 w-4 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"
                        ></div>
                        <span>Registrazione in corso...</span>
                    {:else}
                        <UserPlus class="h-4 w-4" />
                        <span>Registrati</span>
                    {/if}
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-muted-foreground">
                    Hai già un account?
                    <a
                        href="/login"
                        class="font-medium text-primary hover:underline"
                    >
                        Accedi
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
