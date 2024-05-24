import axios from "axios";
import { onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const oEntrenamiento = ref({
    id: 0,
    cod: "",
    nro: "",
    total: "",
    archivo: null,
    _method: "POST",
});

export const useEntrenamientos = () => {
    const { flash } = usePage().props;
    const getEntrenamientos = async () => {
        try {
            const response = await axios.get(route("entrenamientos.listado"), {
                headers: { Accept: "application/json" },
            });
            return response.data.entrenamientos;
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.response?.data
                        ? err.response?.data?.message
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            throw err; // Puedes manejar el error según tus necesidades
        }
    };

    const getEntrenamientosByTipo = async (data) => {
        try {
            const response = await axios.get(route("entrenamientos.byTipo"), {
                headers: { Accept: "application/json" },
                params: data,
            });
            return response.data.entrenamientos;
        } catch (error) {
            console.error("Error:", error);
            throw error; // Puedes manejar el error según tus necesidades
        }
    };

    const getEntrenamientosApi = async (data) => {
        try {
            const response = await axios.get(
                route("entrenamientos.paginado", data),
                {
                    headers: { Accept: "application/json" },
                }
            );
            return response.data.entrenamientos;
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.response?.data
                        ? err.response?.data?.message
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            throw err; // Puedes manejar el error según tus necesidades
        }
    };
    const saveEntrenamiento = async (data) => {
        try {
            const response = await axios.post(
                route("entrenamientos.store", data),
                {
                    headers: { Accept: "application/json" },
                }
            );
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            return response.data;
        } catch (err) {
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
            console.error("Error:", err);
            throw err; // Puedes manejar el error según tus necesidades
        }
    };

    const deleteEntrenamiento = async (id) => {
        try {
            const response = await axios.delete(
                route("entrenamientos.destroy", id),
                {
                    headers: { Accept: "application/json" },
                }
            );
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            return response.data;
        } catch (err) {
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
            console.error("Error:", err);
            throw err; // Puedes manejar el error según tus necesidades
        }
    };

    const setEntrenamiento = (item = null) => {
        if (item) {
            oEntrenamiento.value.id = item.id;
            oEntrenamiento.value.cod = item.cod;
            oEntrenamiento.value.nro = item.nro;
            oEntrenamiento.value.total = item.total;
            oEntrenamiento.value.archivo = item.archivo;
            oEntrenamiento.value._method = "PUT";
            return oEntrenamiento;
        }
        return false;
    };

    const limpiarEntrenamiento = () => {
        oEntrenamiento.value.id = 0;
        oEntrenamiento.value.cod = "";
        oEntrenamiento.value.nro = "";
        oEntrenamiento.value.total = "";
        oEntrenamiento.value.archivo = "";
        oEntrenamiento.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oEntrenamiento,
        getEntrenamientos,
        getEntrenamientosApi,
        saveEntrenamiento,
        deleteEntrenamiento,
        setEntrenamiento,
        limpiarEntrenamiento,
        getEntrenamientosByTipo,
    };
};
