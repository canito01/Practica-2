import { studentsAPI } from '../api/studentsAPI.js';
import { subjectsAPI } from '../api/subjectsAPI.js';
import { studentsSubjectsAPI } from '../api/studentsSubjectsAPI.js';


document.addEventListener('DOMContentLoaded', () => 
{
    initSelects();
    setupFormHandler();
    loadRelations();
    setupCancelHandler();
});

async function initSelects() 
{
    try 
    {
        // Cargar estudiantes en sus respectivos select
        const students = await studentsAPI.fetchAll();
        const studentSelect = document.getElementById('studentIdSelect');
        students.forEach(s => 
        {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = s.fullname;
            studentSelect.appendChild(option); //appendchild agrega el option al select 
        });

        // Cargar materias
        const subjects = await subjectsAPI.fetchAll();
        const subjectSelect = document.getElementById('subjectIdSelect');
        subjects.forEach(sub => 
        {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.name;
            subjectSelect.appendChild(option);
        });
    } 
    catch (err) 
    {
        console.error('Error cargando estudiantes o materias:', err.message);
    }
}


function setupFormHandler() 
{
    const form = document.getElementById('relationForm');
    form.addEventListener('submit', async e => 
    {
        e.preventDefault();

        const relation = getFormData();

        try 
        {
            if (relation.id) 
            {
                await studentsSubjectsAPI.update(relation);
            } 
            else 
            {   
                
                 //traer todas las relaciones actuales
                const allRelations = await studentsSubjectsAPI.fetchAll();
                // Buscar si ya existe una con ese estudiante y esa materia
                const alreadyExists = allRelations.some(r => r.student_id === relation.student_id && r.subject_id === relation.subject_id);
                 if (alreadyExists) 
                {
                    alert('La relación entre ese estudiante y materia ya existe.');
                    return;
                }
                await studentsSubjectsAPI.create(relation);
            }
            clearForm();
            loadRelations();
        } 
        catch (err) 
        {
            if (err instanceof Response) {
                const res = await err.json();
                 if (res.error) {
                    alert(res.error);  // ← muestra el mensaje del backend
            }
            } else {
                 console.error('Error guardando relación:', err.message);
                  }      
        }
    });
}

function getFormData() 
{
    return{
        id: document.getElementById('relationId').value.trim(), //funciones del DOM para obtener los valores de los campos del formulario
        student_id: document.getElementById('studentIdSelect').value, //funcionan con los id provenientes del HTML
        subject_id: document.getElementById('subjectIdSelect').value,
        approved: document.getElementById('approved').checked ? 1 : 0  // Convertir el checkbox a 1 o 0 para el backend
    };
}

function clearForm() 
{
    document.getElementById('relationForm').reset();
    document.getElementById('relationId').value = '';
}

async function loadRelations() 
{
    try 
    {
        const relations = await studentsSubjectsAPI.fetchAll();
        
        /**
         * DEBUG
         */
        //console.log(relations);

        /**
         * En JavaScript: Cualquier string que no esté vacío ("") es considerado truthy.
         * Entonces "0" (que es el valor que llega desde el backend) es truthy,
         * ¡aunque conceptualmente sea falso! por eso: 
         * Se necesita convertir ese string "0" a un número real 
         * o asegurarte de comparar el valor exactamente. 
         * Con el siguiente código se convierten todos los string approved a enteros.
         */
        relations.forEach(rel => 
        {
            rel.approved = Number(rel.approved);
        });
        
        renderRelationsTable(relations);
    } 
    catch (err) 
    {
        console.error('Error cargando inscripciones:', err.message);
    }
}

function renderRelationsTable(relations) 
{
    const tbody = document.getElementById('relationTableBody');
    tbody.replaceChildren();

    relations.forEach(rel => 
    {
        const tr = document.createElement('tr');

        // tr.appendChild(createCell(rel.fullname || rel.student_id));old
        tr.appendChild(createCell(rel.student_fullname));
        // tr.appendChild(createCell(rel.name || rel.subject_id));old
        tr.appendChild(createCell(rel.subject_name));
        tr.appendChild(createCell(rel.approved ? 'Sí' : 'No'));
        tr.appendChild(createActionsCell(rel));

        tbody.appendChild(tr);
    });
}

function createCell(text) 
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createActionsCell(relation) 
{
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => fillForm(relation));

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDelete(relation.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}

function fillForm(relation) //Llena el formulario con los datos de la relación seleccionada para editar
{
    document.getElementById('relationId').value = relation.id;
    document.getElementById('studentIdSelect').value = relation.student_id;
    document.getElementById('subjectIdSelect').value = relation.subject_id;
    document.getElementById('approved').checked = !!relation.approved;
}

async function confirmDelete(id) 
{
    if (!confirm('¿Estás seguro que deseas borrar esta inscripción?')) return;

    try 
    {
        await studentsSubjectsAPI.remove(id);
        loadRelations();
    } 
    catch (err) 
    {
        console.error('Error al borrar inscripción:', err.message);
    }
}

function setupCancelHandler()  //Esto asegura que si el usuario habia seleccionado un estudiante para editar, al hacer click en el botón Cancelar, se limpie el campo del id del estudiante
{
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.addEventListener('click', () => 
    {
        document.getElementById('studentId').value = '';
    });
}
