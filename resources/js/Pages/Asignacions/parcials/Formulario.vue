<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useAsignacions } from "@/composables/asignacions/useAsignacions";
import { useUsuarios } from "@/composables/usuarios/useUsuarios";
import { useMenu } from "@/composables/useMenu";
import { watch, ref, reactive, computed, onMounted } from "vue";

const { mobile, cambiarUrl } = useMenu();
const { oAsignacion, limpiarAsignacion } = useAsignacions();
let form = useForm(oAsignacion);

const { flash, auth } = usePage().props;
const user = ref(auth.user);
const { getUsuariosByTipo } = useUsuarios();
const listUsuariosRegional = ref([]);
const listUsuariosEncargado = ref([]);

const tituloDialog = computed(() => {
    return oAsignacion.id == 0 ? `Agregar Asignacion` : `Editar Asignacion`;
});

const enviarFormulario = () => {
    let url =
        form["_method"] == "POST"
            ? route("asignacions.store")
            : route("asignacions.update", form.id);

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
            limpiarAsignacion();
            cambiarUrl(route("asignacions.index"));
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

const cargarListas = async () => {};

onMounted(() => {
    if (form.id && form.id != "") {
        cargaMapaGoogle(form.lat, form.lng, true);
    } else {
        cargaMapaGoogle("-16.496059", "-68.133345", true);
    }
    cargarListas();
});
</script>

<template>
    <v-row class="mt-0">
        <v-col cols="12" class="d-flex justify-end">
            <template v-if="mobile">
                <v-btn
                    icon="mdi-arrow-left"
                    class="mr-2"
                    @click="cambiarUrl(route('asignacions.index'))"
                ></v-btn>
                <v-btn icon="mdi-content-save" color="primary"></v-btn>
            </template>
            <template v-if="!mobile">
                <v-btn
                    prepend-icon="mdi-arrow-left"
                    class="mr-2"
                    @click="cambiarUrl(route('asignacions.index'))"
                >
                    Volver</v-btn
                >
                <v-btn
                    :prepend-icon="
                        oAsignacion.id != 0 ? 'mdi-sync' : 'mdi-content-save'
                    "
                    color="primary"
                    @click="enviarFormulario"
                >
                    <span
                        v-text="
                            oAsignacion.id != 0
                                ? 'Actualizar Asignacion'
                                : 'Guardar Asignacion'
                        "
                    ></span
                ></v-btn>
            </template>
        </v-col>
    </v-row>
    <v-row>
        <v-col cols="12" sm="12" md="6" xl="6">
            <v-card>
                <v-card-title class="border-b bg-primary pa-5">
                    <v-icon
                        :icon="form.id == 0 ? 'mdi-plus' : 'mdi-pencil'"
                    ></v-icon>
                    <span class="text-h5" v-html="tituloDialog"></span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <form @submit.prevent="enviarFormulario">
                            <v-row>
                                <v-col cols="12" sm="12" md="12" xl="6">
                                    <v-textarea
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
                                        label="Nombre de la Asignacion*"
                                        rows="1"
                                        auto-grow
                                        density="compact"
                                        v-model="form.nombre"
                                    ></v-textarea>
                                </v-col>
                                <v-col cols="12" sm="12" md="12" xl="6">
                                    <v-select
                                        :hide-details="
                                            form.errors?.gerente_regional_id
                                                ? false
                                                : true
                                        "
                                        :error="
                                            form.errors?.gerente_regional_id
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            form.errors?.gerente_regional_id
                                                ? form.errors
                                                      ?.gerente_regional_id
                                                : ''
                                        "
                                        clearable
                                        variant="outlined"
                                        label="Seleccionar Gerente Regional*"
                                        :items="listUsuariosRegional"
                                        item-value="id"
                                        item-title="full_name"
                                        required
                                        density="compact"
                                        v-model="form.gerente_regional_id"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12" sm="12" md="12" xl="6">
                                    <v-select
                                        :hide-details="
                                            form.errors?.encargado_asignacion_id
                                                ? false
                                                : true
                                        "
                                        :error="
                                            form.errors?.encargado_asignacion_id
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            form.errors?.encargado_asignacion_id
                                                ? form.errors
                                                      ?.encargado_asignacion_id
                                                : ''
                                        "
                                        clearable
                                        variant="outlined"
                                        label="Seleccionar Encargado de Asignacion*"
                                        :items="listUsuariosEncargado"
                                        item-value="id"
                                        item-title="full_name"
                                        required
                                        density="compact"
                                        v-model="form.encargado_asignacion_id"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12" sm="12" md="12" xl="6">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.fecha_pent
                                                ? false
                                                : true
                                        "
                                        :error="
                                            form.errors?.fecha_pent
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            form.errors?.fecha_pent
                                                ? form.errors?.fecha_pent
                                                : ''
                                        "
                                        variant="outlined"
                                        type="date"
                                        label="Fecha de Plazo de Entrega*"
                                        required
                                        density="compact"
                                        v-model="form.fecha_pent"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="12" md="12" xl="6">
                                    <v-text-field
                                        :hide-details="
                                            form.errors?.fecha_peje
                                                ? false
                                                : true
                                        "
                                        :error="
                                            form.errors?.fecha_peje
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            form.errors?.fecha_peje
                                                ? form.errors?.fecha_peje
                                                : ''
                                        "
                                        variant="outlined"
                                        type="date"
                                        label="Fecha de Plazo de Ejecución*"
                                        required
                                        density="compact"
                                        v-model="form.fecha_peje"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="12" md="12" xl="6">
                                    <v-textarea
                                        :hide-details="
                                            form.errors?.descripcion
                                                ? false
                                                : true
                                        "
                                        :error="
                                            form.errors?.descripcion
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            form.errors?.descripcion
                                                ? form.errors?.descripcion
                                                : ''
                                        "
                                        variant="outlined"
                                        label="Descripción"
                                        rows="1"
                                        auto-grow
                                        density="compact"
                                        v-model="form.descripcion"
                                    ></v-textarea>
                                </v-col>
                            </v-row>
                        </form>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<style scoped>
#google_map {
    width: 100%;
    height: 500px;
}
</style>
