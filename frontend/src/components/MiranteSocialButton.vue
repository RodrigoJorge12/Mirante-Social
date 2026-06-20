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
  background-color: #059669 !important;
  border-color: #059669 !important;
  color: #fff !important;
  font-family: 'Nunito', sans-serif;
  font-weight: 800;
  font-size: 1rem;
  border-radius: 12px;
  padding: 10px 28px;
  letter-spacing: 0.01em;
  transition: background-color 0.2s, box-shadow 0.2s, transform 0.15s;
}

.mirante-social-button:hover:not(:disabled) {
  background-color: #047857 !important;
  border-color: #047857 !important;
  box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
  transform: translateY(-2px);
}

.mirante-social-button:active:not(:disabled) {
  background-color: #065F46 !important;
  border-color: #065F46 !important;
  transform: translateY(0);
  box-shadow: none;
}

.mirante-social-button:disabled,
.mirante-social-button.is-disabled {
  background-color: #A7F3D0 !important;
  border-color: #A7F3D0 !important;
  color: #fff !important;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}
</style>
