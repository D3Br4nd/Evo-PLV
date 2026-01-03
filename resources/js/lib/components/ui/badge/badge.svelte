<script lang="ts">
  import { cva, type VariantProps } from "class-variance-authority";
  import { cn } from "@/lib/utils/cn";

  const badgeVariants = cva(
    "inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-xs font-medium transition-colors [&_svg]:size-3.5",
    {
      variants: {
        variant: {
          default: "border-transparent bg-primary text-primary-foreground",
          secondary: "border-transparent bg-secondary text-secondary-foreground",
          destructive:
            "border-transparent bg-destructive text-destructive-foreground",
          outline: "text-foreground",
        },
      },
      defaultVariants: {
        variant: "default",
      },
    },
  );

  type Props = VariantProps<typeof badgeVariants> & { class?: string };
  let { variant, class: className, children } = $props<Props & { children?: any }>();
</script>

<span class={cn(badgeVariants({ variant }), className)}>
  {@render children?.()}
</span>


