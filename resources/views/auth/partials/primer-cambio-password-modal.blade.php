<div x-data="primerCambioPasswordModal()" 
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-primer-password-title">

    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full p-6 sm:p-8 text-left relative overflow-hidden"
         @click.away.prevent="">
        
        <!-- Encabezado con icono institucional -->
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-brand-gold-100 text-brand-gold-700 mb-3.5 ring-8 ring-brand-gold-50">
                <i class="fas fa-shield-alt text-2xl" aria-hidden="true"></i>
            </div>
            <h2 id="modal-primer-password-title" class="text-xl sm:text-2xl font-bold text-brand-navy">
                Establece tu Contraseña
            </h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1.5 leading-relaxed">
                Bienvenido a <strong>INVEC - CCISUR</strong>. Por políticas de seguridad institucional, debes cambiar la contraseña provisional por una clave personal antes de continuar.
            </p>
        </div>

        <form method="POST" action="{{ route('primer-cambio-password') }}" @submit.prevent="submitForm($event)" class="space-y-4">
            @csrf

            <!-- Nueva Contraseña -->
            <div>
                <label for="primer_password" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider">
                    Nueva Contraseña Personal <span class="text-red-500">*</span>
                </label>
                <div class="relative mt-1">
                    <input :type="showPass ? 'text' : 'password'" 
                           name="password" 
                           id="primer_password" 
                           required 
                           autocomplete="new-password"
                           placeholder="Mínimo 8 caracteres"
                           :class="errors.password ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300'"
                           class="block w-full rounded-lg text-sm shadow-sm pr-10 focus:border-brand-gold focus:ring-brand-gold">
                    <button type="button" 
                            @click="showPass = !showPass" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fas text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                    </button>
                </div>
                <template x-if="errors.password">
                    <p class="text-red-600 text-xs mt-1 font-medium" x-text="errors.password[0]"></p>
                </template>
            </div>

            <!-- Confirmar Nueva Contraseña -->
            <div>
                <label for="primer_password_confirmation" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider">
                    Confirmar Contraseña <span class="text-red-500">*</span>
                </label>
                <div class="relative mt-1">
                    <input :type="showPassConfirm ? 'text' : 'password'" 
                           name="password_confirmation" 
                           id="primer_password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Repite tu nueva contraseña"
                           class="block w-full rounded-lg text-sm border-gray-300 shadow-sm pr-10 focus:border-brand-gold focus:ring-brand-gold">
                    <button type="button" 
                            @click="showPassConfirm = !showPassConfirm" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fas text-xs" :class="showPassConfirm ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="rounded-lg bg-blue-50 border border-blue-200 p-3 text-xs text-blue-800 flex items-start gap-2">
                <i class="fas fa-info-circle text-blue-600 text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                <span>Esta será tu contraseña permanente para ingresar a INVEC. Guárdala en un lugar seguro.</span>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        :disabled="loading"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-brand-navy text-white text-sm font-semibold shadow hover:bg-brand-navy-800 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition disabled:opacity-50 cursor-pointer">
                    <i class="fas text-xs" :class="loading ? 'fa-spinner fa-spin' : 'fa-check'" aria-hidden="true"></i>
                    <span x-text="loading ? 'Guardando contraseña...' : 'Guardar y Continuar al Sistema'"></span>
                </button>
            </div>
        </form>

        <!-- Opción de cerrar sesión si el usuario no desea cambiarla en este momento -->
        <div class="mt-4 pt-3 border-t border-gray-100 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 transition underline">
                    Cerrar sesión y salir
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function primerCambioPasswordModal() {
        return {
            loading: false,
            errors: {},
            showPass: false,
            showPassConfirm: false,

            async submitForm(event) {
                this.loading = true;
                this.errors = {};
                const form = event.target;
                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value
                        }
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        this.errors = data.errors || {};
                        this.loading = false;
                        return;
                    }

                    if (!response.ok) {
                        throw new Error('Error al actualizar la contraseña');
                    }

                    const data = await response.json();
                    window.location.href = data.redirect || '{{ route('dashboard') }}';
                } catch (err) {
                    this.loading = false;
                    alert('Ocurrió un error al guardar la contraseña. Por favor intenta de nuevo.');
                }
            }
        };
    }
</script>
