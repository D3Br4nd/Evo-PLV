<script>
    import { useForm } from "@inertiajs/svelte";
    import { Mail, Lock, LogIn } from "lucide-svelte";

    // Form state
    const form = useForm({
        email: "",
        password: "",
        remember: false,
    });

    function handleSubmit(e) {
        e.preventDefault();
        $form.post("/login");
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
                Accedi al pannello amministrativo
            </p>
        </div>

        <!-- Login Form -->
        <div class="rounded-lg border border-border bg-card p-8 shadow-sm">
            <form onsubmit={handleSubmit} class="space-y-6">
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
                            placeholder="admin@prolocoventicanese.it"
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

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input
                        id="remember"
                        type="checkbox"
                        bind:checked={$form.remember}
                        class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                    />
                    <label
                        for="remember"
                        class="ml-2 block text-sm text-foreground"
                    >
                        Ricordami
                    </label>
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
                        <span>Accesso in corso...</span>
                    {:else}
                        <LogIn class="h-4 w-4" />
                        <span>Accedi</span>
                    {/if}
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-muted-foreground">
                    Non hai un account?
                    <a
                        href="/register"
                        class="font-medium text-primary hover:underline"
                    >
                        Registrati
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
