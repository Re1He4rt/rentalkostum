import Alpine from 'alpinejs'

window.Alpine = Alpine

// Register global Alpine stores (opsional)
document.addEventListener('alpine:init', () => {
    Alpine.store('notif', {
        show: false,
        message: '',
        type: 'success',
        open(message, type = 'success') {
            this.message = message
            this.type = type
            this.show = true
            setTimeout(() => this.show = false, 3000)
        }
    })
})

Alpine.start()
