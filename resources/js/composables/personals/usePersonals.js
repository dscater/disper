import axios from "axios";
import { onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const oPersonal = ref({
    id: 0,
    nombre: "",
    paterno: "",
    materno: "",
    ci: "",
    ci_exp: "",
    estado_civil: "",
    genero: "",
    dir: "",
    correo: "",
    cel: "",
    nombre_contacto: "",
    cel_contacto: "",
    foto: "",
    estado: "",
    _method: "POST",
});

export const usePersonals = () => {
    const { flash } = usePage().props;
    const getPersonals = async () => {
        try {
            const response = await axios.get(route("personals.listado"), {
                headers: { Accept: "application/json" },
            });
            return response.data.personals;
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

    const getPersonalsByTipo = async (data) => {
        try {
            const response = await axios.get(route("personals.byTipo"), {
                headers: { Accept: "application/json" },
                params: data,
            });
            return response.data.personals;
        } catch (error) {
            console.error("Error:", error);
            throw error; // Puedes manejar el error según tus necesidades
        }
    };

    const getPersonalsApi = async (data) => {
        try {
            const response = await axios.get(
                route("personals.paginado", data),
                {
                    headers: { Accept: "application/json" },
                }
            );
            return response.data.personals;
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
    const savePersonal = async (data) => {
        try {
            const response = await axios.post(route("personals.store", data), {
                headers: { Accept: "application/json" },
            });
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

    const deletePersonal = async (id) => {
        try {
            const response = await axios.delete(
                route("personals.destroy", id),
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

    const setPersonal = (item = null) => {
        if (item) {
            oPersonal.value.id = item.id;
            oPersonal.value.nombre = item.nombre;
            oPersonal.value.paterno = item.paterno;
            oPersonal.value.materno = item.materno;
            oPersonal.value.ci = item.ci;
            oPersonal.value.ci_exp = item.ci_exp;
            oPersonal.value.estado_civil = item.estado_civil;
            oPersonal.value.genero = item.genero;
            oPersonal.value.dir = item.dir;
            oPersonal.value.correo = item.correo;
            oPersonal.value.cel = item.cel;
            oPersonal.value.nombre_contacto = item.nombre_contacto;
            oPersonal.value.cel_contacto = item.cel_contacto;
            oPersonal.value.foto = item.foto;
            oPersonal.value.estado = item.estado;
            oPersonal.value._method = "PUT";
            return oPersonal;
        }
        return false;
    };

    const limpiarPersonal = () => {
        oPersonal.value.id = 0;
        oPersonal.value.nombre = "";
        oPersonal.value.paterno = "";
        oPersonal.value.materno = "";
        oPersonal.value.ci = "";
        oPersonal.value.ci_exp = "";
        oPersonal.value.estado_civil = "";
        oPersonal.value.genero = "";
        oPersonal.value.dir = "";
        oPersonal.value.correo = "";
        oPersonal.value.cel = "";
        oPersonal.value.nombre_contacto = "";
        oPersonal.value.cel_contacto = "";
        oPersonal.value.foto = "";
        oPersonal.value.estado = "";
        oPersonal.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oPersonal,
        getPersonals,
        getPersonalsApi,
        savePersonal,
        deletePersonal,
        setPersonal,
        limpiarPersonal,
        getPersonalsByTipo,
    };
};
