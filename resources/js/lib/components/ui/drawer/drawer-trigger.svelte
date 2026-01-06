<script>
	import { Drawer as DrawerPrimitive } from "vaul-svelte";

	let { ref = $bindable(null), children, ...restProps } = $props();
</script>

<DrawerPrimitive.Trigger bind:ref data-slot="drawer-trigger" {...restProps}>
	{@render children?.()}
</DrawerPrimitive.Trigger>