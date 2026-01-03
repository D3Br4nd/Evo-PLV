<script lang="ts">
  import { Dialog } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<Dialog.Trigger {...rest}>
  {@render children?.()}
</Dialog.Trigger>


