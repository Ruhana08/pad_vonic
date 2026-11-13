// url=https://www.figma.com/design/g9cQKH86tyM6CceKV6wyMx/UI-Kelompok-21?node-id=640:16162
// source=resources/views/components/button.blade.php
// component=Button
import figma from 'figma'

const instance = figma.selectedInstance

const label = instance.findText('Button')?.textContent ?? 'Button'

export default {
  example: figma.code`<x-button variant="primary">${label}</x-button>`,
  id: 'button',
  metadata: {
    nestable: true,
  },
}
