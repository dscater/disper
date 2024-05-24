import axios from "axios";
import { onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const oAsignacion = ref({
    id: 0,
    cod: "",
    nro: "",
    total: "",
    archivo: null,
    _method: "POST",
});

export const useAsignacions = () => {
    const { flash } = usePage().props;
    const getAsignacions = async () => {
        try {
            const response = await axios.get(route("asignacions.listado"), {
                headers: { Accept: "application/json" },
            });
            return response.data.asignacions;
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

    const getAsignacionsByTipo = async (data) => {
        try {
            const response = await axios.get(route("asignacions.byTipo"), {
                headers: { Accept: "application/json" },
                params: data,
            });
            return response.data.asignacions;
        } catch (error) {
            console.error("Error:", error);
            throw error; // Puedes manejar el error según tus necesidades
        }
    };

    const getAsignacionsApi = async (data) => {
        try {
            const response = await axios.get(
                route("asignacions.paginado", data),
                {
                    headers: { Accept: "application/json" },
                }
            );
            return response.data.asignacions;
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
    const saveAsignacion = async (data) => {
        try {
            const response = await axios.post(
                route("asignacions.store", data),
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

    const deleteAsignacion = async (id) => {
        try {
            const response = await axios.delete(
                route("asignacions.destroy", id),
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

    const setAsignacion = (item = null) => {
        if (item) {
            oAsignacion.value.id = item.id;
            oAsignacion.value.cod = item.cod;
            oAsignacion.value.nro = item.nro;
            oAsignacion.value.total = item.total;
            oAsignacion.value.archivo = item.archivo;
            oAsignacion.value._method = "PUT";
            return oAsignacion;
        }
        return false;
    };

    const limpiarAsignacion = () => {
        oAsignacion.value.id = 0;
        oAsignacion.value.cod = "";
        oAsignacion.value.nro = "";
        oAsignacion.value.total = "";
        oAsignacion.value.archivo = "";
        oAsignacion.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oAsignacion,
        getAsignacions,
        getAsignacionsApi,
        saveAsignacion,
        deleteAsignacion,
        setAsignacion,
        limpiarAsignacion,
        getAsignacionsByTipo,
    };
};
