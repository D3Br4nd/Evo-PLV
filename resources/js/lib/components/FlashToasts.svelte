<script>
    /* eslint-disable */
    /* global $state, $derived, $effect */
    import { page } from "@inertiajs/svelte";
    import { toast } from "svelte-sonner";

    let flash = $derived($page.props.flash || {});

    let lastSuccess = $state(null);
    let lastError = $state(null);

    $effect(() => {
        const s = flash?.success;
        if (s && s !== lastSuccess) {
            toast.success(s);
            lastSuccess = s;
        }
    });

    $effect(() => {
        const e = flash?.error;
        if (e && e !== lastError) {
            toast.error(e);
            lastError = e;
        }
    });
</script>


