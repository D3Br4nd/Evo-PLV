<script lang="ts">
  import { Dialog } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<Dialog.Root {...rest}>
  {@render children?.()}
</Dialog.Root>


