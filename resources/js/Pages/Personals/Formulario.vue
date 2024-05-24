<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { usePersonals } from "@/composables/personals/usePersonals";
import { watch, ref, computed, defineEmits } from "vue";
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
    accion_dialog: {
        type: Number,
        default: 0,
    },
});

const { oPersonal, limpiarPersonal } = usePersonals();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
let form = useForm(oPersonal.value);
watch(
    () => props.open_dialog,
    (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            form = useForm(oPersonal.value);
        }
    }
);
watch(
    () => props.accion_dialog,
    (newValue) => {
        accion.value = newValue;
    }
);

const { flash } = usePage().props;

const listExpedido = [
    { value: "LP", label: "La Paz" },
    { value: "CB", label: "Cochabamba" },
    { value: "SC", label: "Santa Cruz" },
    { value: "CH", label: "Chuquisaca" },
    { value: "OR", label: "Oruro" },
    { value: "PT", label: "Potosi" },
    { value: "TJ", label: "Tarija" },
    { value: "PD", label: "Pando" },
    { value: "BN", label: "Beni" },
];

const listEstados = [
    { value: "ACTIVO", label: "ACTIVO" },
    { value: "INACTIVO", label: "INACTIVO" },
];

const foto = ref(null);
function cargaArchivo(e, key) {
    form[key] = null;
    form[key] = e.target.files[0];
}

const tituloDialog = computed(() => {
    return accion.value == 0 ? `Agregar Personal` : `Editar Personal`;
});

const enviarFormulario = () => {
    let url =
        form["_method"] == "POST"
            ? route("personals.store")
            : route("personals.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            limpiarPersonal();
            emits("envio-formulario");
        },
        onError: (err) => {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.error
                        ? err.error
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
        },
    });
};

const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

watch(dialog, (newVal) => {
    if (!newVal) {
        emits("cerrar-dialog");
    }
});

const cerrarDialog = () => {
    dialog.value = false;
};
</script>

<template>
    <v-row justify="center">
        <v-dialog v-model="dialog" width="1024" persistent scrollable>
            <v-card>
                <v-card-title class="border-b bg-cyan-accent-4 pa-5">
                    <v-icon
                        icon="mdi-close"
                        class="float-right"
                        @click="cerrarDialog"
                    ></v-icon>

                    <v-icon
                        :icon="accion == 0 ? 'mdi-plus' : 'mdi-pencil'"
                    ></v-icon>
                    <span class="text-h5" v-html="tituloDialog"></span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <form>
                            <v-row>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.nombre ? false : true
                                        "
                                        :error="
                                            form.errors?.nombre ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.nombre
                                                ? form.errors?.nombre
                                                : ''
                                        "
                                        variant="outlined"
                                        label="Nombre*"
                                        required
                                        density="compact"
                                        v-model="form.nombre"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.paterno ? false : true
                                        "
                                        :error="
                                            form.errors?.paterno ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.paterno
                                                ? form.errors?.paterno
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        label="Apellido Paterno"
                                        v-model="form.paterno"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.materno ? false : true
                                        "
                                        :error="
                                            form.errors?.materno ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.materno
                                                ? form.errors?.materno
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        label="Apellido Materno"
                                        v-model="form.materno"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.ci ? false : true
                                        "
                                        :error="form.errors?.ci ? true : false"
                                        :error-messages="
                                            form.errors?.ci
                                                ? form.errors?.ci
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        label="C.I.*"
                                        v-model="form.ci"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-select
                                        :hide-details="
                                            form.errors?.ci_exp ? false : true
                                        "
                                        :error="
                                            form.errors?.ci_exp ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.ci_exp
                                                ? form.errors?.ci_exp
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        clearable
                                        :items="listExpedido"
                                        item-value="value"
                                        item-title="label"
                                        label="Expedido*"
                                        no-data-text="Sin registros"
                                        v-model="form.ci_exp"
                                        required
                                    ></v-select>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-select
                                        :hide-details="
                                            form.errors?.estado_civil
                                                ? false
                                                : true
                                        "
                                        :error="
                                            form.errors?.estado_civil
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            form.errors?.estado_civil
                                                ? form.errors?.estado_civil
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        clearable
                                        :items="[
                                            'SOLTERO',
                                            'CASADO',
                                            'DIVORCIADO',
                                            'VIUDO',
                                        ]"
                                        label="Estado Civil*"
                                        no-data-text="Sin registros"
                                        v-model="form.estado_civil"
                                        required
                                    ></v-select>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-select
                                        :hide-details="
                                            form.errors?.genero ? false : true
                                        "
                                        :error="
                                            form.errors?.genero ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.genero
                                                ? form.errors?.genero
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        clearable
                                        :items="['HOMBRE', 'MUJER']"
                                        label="Género*"
                                        no-data-text="Sin registros"
                                        v-model="form.genero"
                                        required
                                    ></v-select>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.dir ? false : true
                                        "
                                        :error="form.errors?.dir ? true : false"
                                        :error-messages="
                                            form.errors?.dir
                                                ? form.errors?.dir
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        label="Dirección*"
                                        v-model="form.dir"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.correo ? false : true
                                        "
                                        :error="
                                            form.errors?.correo ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.correo
                                                ? form.errors?.correo
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        label="Email"
                                        v-model="form.correo"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.cel ? false : true
                                        "
                                        :error="form.errors?.cel ? true : false"
                                        :error-messages="
                                            form.errors?.cel
                                                ? form.errors?.cel
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        label="Celular"
                                        v-model="form.cel"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.nombre_contacto
                                                ? false
                                                : true
                                        "
                                        :error="
                                            form.errors?.nombre_contacto
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            form.errors?.nombre_contacto
                                                ? form.errors?.nombre_contacto
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        label="Nombre Contacto"
                                        v-model="form.nombre_contacto"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.cel_contacto
                                                ? false
                                                : true
                                        "
                                        :error="
                                            form.errors?.cel_contacto
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            form.errors?.cel_contacto
                                                ? form.errors?.cel_contacto
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        label="Celular Contacto"
                                        v-model="form.cel_contacto"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-file-input
                                        :hide-details="
                                            form.errors?.foto ? false : true
                                        "
                                        :error="
                                            form.errors?.foto ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.foto
                                                ? form.errors?.foto
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        accept="image/png, image/jpeg, image/bmp"
                                        placeholder="Foto"
                                        prepend-icon="mdi-camera"
                                        label="Foto"
                                        @change="cargaArchivo($event, 'foto')"
                                        ref="foto"
                                    ></v-file-input>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-select
                                        :hide-details="
                                            form.errors?.estado ? false : true
                                        "
                                        :error="
                                            form.errors?.estado ? true : false
                                        "
                                        :error-messages="
                                            form.errors?.estado
                                                ? form.errors?.estado
                                                : ''
                                        "
                                        density="compact"
                                        variant="outlined"
                                        clearable
                                        :items="listEstados"
                                        item-value="value"
                                        item-title="label"
                                        label="Estado*"
                                        no-data-text="Sin registros"
                                        v-model="form.estado"
                                        required
                                    ></v-select>
                                </v-col>
                            </v-row>
                        </form>
                    </v-container>
                </v-card-text>
                <v-card-actions class="border-t">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey-darken-4"
                        variant="text"
                        @click="cerrarDialog"
                    >
                        Cancelar
                    </v-btn>
                    <v-btn
                        class="bg-cyan-accent-4"
                        prepend-icon="mdi-content-save"
                        @click="enviarFormulario"
                    >
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
