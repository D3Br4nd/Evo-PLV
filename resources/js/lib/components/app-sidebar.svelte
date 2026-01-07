<script>
	import DashboardIcon from "@tabler/icons-svelte/icons/dashboard";
	import FolderIcon from "@tabler/icons-svelte/icons/folder";
	import InnerShadowTopIcon from "@tabler/icons-svelte/icons/inner-shadow-top";
	import UsersIcon from "@tabler/icons-svelte/icons/users";
	import CalendarIcon from "@tabler/icons-svelte/icons/calendar";
	import FileDescriptionIcon from "@tabler/icons-svelte/icons/file-description";
	import NavDocuments from "./nav-documents.svelte";
	import NavMain from "./nav-main.svelte";
	import NavUser from "./nav-user.svelte";
	import * as Sidebar from "@/lib/components/ui/sidebar/index.js";
	import { page } from "@inertiajs/svelte";

	const data = {
		user: null,
		navMain: [
			{
				title: "Dashboard",
				url: "/admin/dashboard",
				icon: DashboardIcon,
			},
			{
				title: "Soci",
				url: "/admin/members",
				icon: UsersIcon,
			},
			{
				title: "Eventi",
				url: "/admin/events",
				icon: CalendarIcon,
			},
			{
				title: "Progetti",
				url: "/admin/projects",
				icon: FolderIcon,
			},
		],
		documents: [
			{
				name: "Contenuti",
				url: "/admin/content-pages",
				icon: FileDescriptionIcon,
			},
		],
	};

	let { ...restProps } = $props();

	let user = $derived($page.props.auth?.user);
</script>

<Sidebar.Root collapsible="offcanvas" {...restProps}>
	<Sidebar.Header>
		<Sidebar.Menu>
			<Sidebar.MenuItem>
				<Sidebar.MenuButton
					size="lg"
					class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
				>
					{#snippet child({ props })}
						<a href="/admin/dashboard" {...props}>
							<div
								class="flex aspect-square size-8 items-center justify-center rounded-lg overflow-hidden"
							>
								<img
									src="/logo.png"
									alt="Pro Loco Logo"
									class="size-full object-cover"
								/>
							</div>
							<div
								class="grid flex-1 text-left text-sm leading-tight"
							>
								<span class="truncate font-semibold"
									>Pro Loco Venticanese</span
								>
								<span class="truncate text-xs">Evolution</span>
							</div>
						</a>
					{/snippet}
				</Sidebar.MenuButton>
			</Sidebar.MenuItem>
		</Sidebar.Menu>
	</Sidebar.Header>
	<Sidebar.Content>
		<NavMain items={data.navMain} />
		<NavDocuments items={data.documents} />
	</Sidebar.Content>
	<Sidebar.Footer>
		<NavUser {user} />
	</Sidebar.Footer>
</Sidebar.Root>
