<script lang="ts">
  import { Dialog } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<Dialog.Close {...rest}>
  {@render children?.()}
</Dialog.Close>


