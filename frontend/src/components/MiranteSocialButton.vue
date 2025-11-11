<script setup>
import { defineProps, defineEmits } from 'vue'
import { ElButton } from 'element-plus'

const props = defineProps (
{
    type: {
      type: String,
      default: 'primary',
    },
    loading: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    icon: {
      type: [String, Object],
      default: null,
    },
  }
)

const emit = defineEmits(['click'])

function handleClick(event) {
  if (props.disabled || props.loading) return
  emit('click', event)
}
</script>

<template>
  <ElButton
    :type="props.type"
    :loading="props.loading"
    :disabled="props.disabled"
    @click="handleClick"
    class="mirante-social-button"
  >
    <slot name="icon" />
    <slot />
  </ElButton>
</template>

<style scoped>
.mirante-social-button {
  background-color: #10b981;
}

/* .mirante-social-button:hover:not(:disabled) {
@apply brightness-110;
} */
</style>
