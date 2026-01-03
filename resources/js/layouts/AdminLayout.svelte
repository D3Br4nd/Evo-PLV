<script>
    import { Link, router, page } from "@inertiajs/svelte";
    import {
        Home,
        Users,
        Calendar,
        Kanban,
        Menu,
        LogOut,
        FileText,
    } from "lucide-svelte";
    import { Button } from "@/lib/components/ui/button";
    import ThemeToggle from "@/lib/components/theme-toggle.svelte";
    import * as DropdownMenu from "@/lib/components/ui/dropdown-menu";
    import * as Sheet from "@/lib/components/ui/sheet";

    // Props
    // Admin pages pass `title` (e.g. <AdminLayout title="Soci">) — use it for <title>.
    let { title = "Admin", children } = $props();

    // State for mobile menu
    let mobileMenuOpen = $state(false);

    // Get authenticated user from Inertia page props
    let user = $derived($page.props.auth.user);

    // Get user initials for avatar
    function getUserInitials(name) {
        if (!name) return "U";
        return name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase()
            .slice(0, 2);
    }

    // Navigation items
    const navItems = [
        { href: "/admin/dashboard", label: "Dashboard", icon: Home },
        { href: "/admin/members", label: "Soci", icon: Users },
        { href: "/admin/events", label: "Eventi", icon: Calendar },
        { href: "/admin/projects", label: "Progetti", icon: Kanban },
        { href: "/admin/content-pages", label: "Contenuti", icon: FileText },
    ];
</script>

<svelte:head>
    <title>{title} · Pro Loco Admin</title>
</svelte:head>

<div class="min-h-screen bg-background text-foreground">
    <!-- Desktop Sidebar -->
    <aside
        class="fixed left-0 top-0 z-40 h-screen w-64 border-r border-border bg-card hidden lg:block"
    >
        <div class="flex h-full flex-col">
            <!-- Logo/Brand -->
            <div
                class="flex h-16 items-center gap-3 border-b border-border px-6"
            >
                <img
                    src="/logo.png"
                    alt="Pro Loco"
                    class="h-8 w-8 object-contain"
                />
                <h1 class="text-lg font-semibold text-foreground">
                    Pro Loco Admin
                </h1>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 px-3 py-4">
                {#each navItems as item}
                    {@const active =
                        $page.url.pathname === item.href ||
                        ($page.url.pathname.startsWith(item.href + "/") &&
                            item.href !== "/admin/dashboard")}
                    <Link
                        href={item.href}
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors
                          {active
                            ? 'bg-accent text-accent-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'}"
                    >
                        <item.icon class="h-5 w-5" />
                        <span>{item.label}</span>
                    </Link>
                {/each}
            </nav>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:pl-64">
        <!-- Top Bar -->
        <header
            class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-border bg-background px-4 sm:px-6"
        >
            <!-- Mobile Menu Trigger -->
            <Sheet.Root bind:open={mobileMenuOpen}>
                <Sheet.Trigger>
                    {#snippet child({ props })}
                        <Button
                            {...props}
                            variant="ghost"
                            size="icon"
                            class="lg:hidden"
                            aria-label="Apri menu"
                        >
                            <Menu class="h-6 w-6" />
                        </Button>
                    {/snippet}
                </Sheet.Trigger>
                <Sheet.Content side="left" class="p-0">
                    <div class="flex h-full flex-col">
                        <div
                            class="flex h-16 items-center justify-between border-b border-border px-6"
                        >
                            <div class="flex items-center gap-3">
                                <img
                                    src="/logo.png"
                                    alt="Pro Loco"
                                    class="h-8 w-8 object-contain"
                                />
                                <h1 class="text-lg font-semibold">
                                    Pro Loco Admin
                                </h1>
                            </div>
                            <Sheet.Close>
                                {#snippet child({ props })}
                                    <Button {...props} variant="ghost" size="icon" aria-label="Chiudi menu">
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </Button>
                                {/snippet}
                            </Sheet.Close>
                        </div>
                        <nav class="flex-1 space-y-1 px-3 py-4">
                            {#each navItems as item}
                                {@const active =
                                    $page.url.pathname === item.href ||
                                    ($page.url.pathname.startsWith(item.href + "/") &&
                                        item.href !== "/admin/dashboard")}
                                <Link
                                    href={item.href}
                                    onclick={() => (mobileMenuOpen = false)}
                                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors
                                      {active
                                        ? 'bg-accent text-accent-foreground'
                                        : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'}"
                                >
                                    <item.icon class="h-5 w-5" />
                                    <span>{item.label}</span>
                                </Link>
                            {/each}
                        </nav>
                    </div>
                </Sheet.Content>
            </Sheet.Root>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <ThemeToggle />

            <!-- User Dropdown -->
            <DropdownMenu.Root>
                <DropdownMenu.Trigger>
                    {#snippet child({ props })}
                        <Button {...props} variant="ghost" class="px-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-semibold"
                            >
                                {getUserInitials(user?.name)}
                            </div>
                            <span class="hidden sm:inline"
                                >{user?.name || "Utente"}</span
                            >
                        </Button>
                    {/snippet}
                </DropdownMenu.Trigger>
                <DropdownMenu.Content align="end" sideOffset={8} class="w-48">
                    <DropdownMenu.Label>Account</DropdownMenu.Label>
                    <DropdownMenu.Separator />
                    <DropdownMenu.Item
                        onSelect={() => router.post("/logout")}
                        class="text-destructive data-highlighted:text-destructive"
                    >
                        <LogOut class="h-4 w-4" />
                        Logout
                    </DropdownMenu.Item>
                </DropdownMenu.Content>
            </DropdownMenu.Root>
        </header>

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            {@render children()}
        </main>
    </div>

    <!-- Mobile sidebar handled by Sheet -->
</div>
