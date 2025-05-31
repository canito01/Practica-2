import { studentsAPI } from '../api/studentsAPI.js';

document.addEventListener('DOMContentLoaded', () =>   //document es un objeto que representa el documento HTML cargado en el navegador
{
    loadStudents(); //carga y muestra todos los estudiantes al cargar la página
    setupFormHandler(); //prepara el formulario para que cuando el usuario haga submit, se ejecute la función que guarda los datos
    setupCancelHandler();  // agrega esta línea para configurar el manejador del botón Cancelar
});
  
function setupFormHandler() //configura el envio del formulario
{
    const form = document.getElementById('studentForm'); //obtiene el formulario por su id
    form.addEventListener('submit', async e => 
    {
        e.preventDefault(); //previene el comportamiento por defecto del formulario (que es recargar la página al enviar)
        const student = getFormData(); //obtiene los datos del formulario y los guarda en un objeto student
    
        try 
        {
            if (student.id) 
            {
                await studentsAPI.update(student);
            } 
            else 
            {
                const existingStudents = await studentsAPI.fetchAll(); //se trae todas las materias existentes
                const nameAlreadyExists = existingStudents.some(s => s.name.toLowerCase() === student.name.toLowerCase());
                //some es un metodo que recorre el arreglo de materias y compara el nombre de cada uno



                /**
                 * s es cada materia existente en el array existingStudents
                 * s.name es el nombre de esa materia existente
                 * student.name es el nombre del estudiante que queres agregar
                 */
                if (nameAlreadyExists)
                {
                alert('Ya existe un alumno con ese nombre.');
                return; //frena la ejecución si ya existe una materia con ese nombre
                }
                await studentsAPI.create(student);
            }
            clearForm();
            loadStudents();
        }
        catch (err) //debido a la validacion en el else nunca entra en este bloque, pero lo dejo por si acaso
        {
            console.error(err.message);
        }
    });
}

function setupCancelHandler()  //Esto asegura que si el usuario habia seleccionado un estudiante para editar, al hacer click en el botón Cancelar, se limpie el campo del id del estudiante
{
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.addEventListener('click', () => 
    {
        document.getElementById('studentId').value = '';
    });
}


  
function getFormData() //obtiene los datos del formulario y los devuelve como un objeto
{
    return {
        id: document.getElementById('studentId').value.trim(),
        fullname: document.getElementById('fullname').value.trim(),
        email: document.getElementById('email').value.trim(),
        age: parseInt(document.getElementById('age').value.trim(), 10)
    };
}
  
function clearForm()
{
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
}
  
async function loadStudents()
{
    try 
    {
        const students = await studentsAPI.fetchAll();
        renderStudentTable(students);
    } 
    catch (err) 
    {
        console.error('Error cargando estudiantes:', err.message);
    }
}
  
function renderStudentTable(students)
{
    const tbody = document.getElementById('studentTableBody');
    tbody.replaceChildren();
  
    students.forEach(student => 
    {
        const tr = document.createElement('tr');
    
        tr.appendChild(createCell(student.fullname));
        tr.appendChild(createCell(student.email));
        tr.appendChild(createCell(student.age.toString()));
        tr.appendChild(createActionsCell(student));
    
        tbody.appendChild(tr);
    });
}
  
function createCell(text)
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}
  
function createActionsCell(student)
{
    const td = document.createElement('td');
  
    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => fillForm(student));
  
    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDelete(student.id));
  
    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}
  
function fillForm(student)
{
    document.getElementById('studentId').value = student.id;
    document.getElementById('fullname').value = student.fullname;
    document.getElementById('email').value = student.email;
    document.getElementById('age').value = student.age;
}
  
async function confirmDelete(id) 
{
    if (!confirm('¿Estás seguro que deseas borrar este estudiante?')) return;
  
    try 
    {
               const existingRelations = await studentsSubjectsAPI.fetchAll();
               const StudentIsRelated= existingRelations.some(rel => rel.id === id);    //rel.studentId === id);??
       
               if (StudentIsRelated)
               {
                   alert('No se puede borrar el estudiante porque tiene historial academico.');
                   return; //frena la ejecución si hay materias cargadas 
               }
               // Si no hay materias cargadas, procede a borrar el estudiante
               await studentsAPI.remove(id);
               loadStudents();
    } 
    catch (err) 
    {
        console.error('Error al borrar:', err.message);
    }
}
  

