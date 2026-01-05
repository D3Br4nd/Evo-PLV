<script>
	import DashboardIcon from "@tabler/icons-svelte/icons/dashboard";
	import FolderIcon from "@tabler/icons-svelte/icons/folder";
	import HelpIcon from "@tabler/icons-svelte/icons/help";
	import InnerShadowTopIcon from "@tabler/icons-svelte/icons/inner-shadow-top";
	import SettingsIcon from "@tabler/icons-svelte/icons/settings";
	import UsersIcon from "@tabler/icons-svelte/icons/users";
	import CalendarIcon from "@tabler/icons-svelte/icons/calendar";
	import FileDescriptionIcon from "@tabler/icons-svelte/icons/file-description";
	import NavDocuments from "./nav-documents.svelte";
	import NavMain from "./nav-main.svelte";
	import NavSecondary from "./nav-secondary.svelte";
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
		navSecondary: [
			{
				title: "Impostazioni",
				url: "/admin/profile",
				icon: SettingsIcon,
			},
			{
				title: "Aiuto",
				url: "/admin/dashboard",
				icon: HelpIcon,
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
				<Sidebar.MenuButton class="data-[slot=sidebar-menu-button]:!p-1.5">
					{#snippet child({ props })}
						<a href="/admin/dashboard" {...props}>
							<InnerShadowTopIcon class="!size-5" />
							<span class="text-base font-semibold">Pro Loco Admin</span>
						</a>
					{/snippet}
				</Sidebar.MenuButton>
			</Sidebar.MenuItem>
		</Sidebar.Menu>
	</Sidebar.Header>
	<Sidebar.Content>
		<NavMain items={data.navMain} />
		<NavDocuments items={data.documents} />
		<NavSecondary items={data.navSecondary} class="mt-auto" />
	</Sidebar.Content>
	<Sidebar.Footer>
		<NavUser user={user} />
	</Sidebar.Footer>
</Sidebar.Root>