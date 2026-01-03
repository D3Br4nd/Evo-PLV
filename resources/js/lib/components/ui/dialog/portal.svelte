<script lang="ts">
  import { Dialog } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<Dialog.Portal {...rest}>
  {@render children?.()}
</Dialog.Portal>


