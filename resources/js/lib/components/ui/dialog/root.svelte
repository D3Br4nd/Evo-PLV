<script lang="ts">
  import { Dialog } from "bits-ui";

  // Pass-through wrapper for Bits UI Dialog.Root
  let { children, ...rest } = $props<any>();
</script>

<Dialog.Root {...rest}>
  {@render children?.()}
</Dialog.Root>


