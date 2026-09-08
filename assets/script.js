const calendar = document.getElementById('calendar')
const csrfToken = calendar ? calendar.dataset.csrf : ''

let draggedTask = null
let initialPosition = null

function drag(event) {
  draggedTask = event.target.closest('.task')
  initialPosition = event.clientY
}

function drop(event) {
  event.preventDefault()
  if (!draggedTask) return

  const deltaY = event.clientY - initialPosition
  const id = draggedTask.dataset.id
  const newStartTime = Math.round((deltaY / 40) * 3600) // 1 hour = 40px

  fetch('reschedule_task.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken,
    },
    body: JSON.stringify({ id, newStartTime }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) location.reload()
      else alert(data.error || 'Failed to reschedule task.')
    })
    .catch(() => alert('Network error while rescheduling task.'))

  draggedTask = null
}

function deleteTask(button) {
  if (!confirm('Delete this task?')) return

  fetch('delete_task.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken,
    },
    body: JSON.stringify({ id: button.dataset.id }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) location.reload()
      else alert(data.error || 'Failed to delete task.')
    })
    .catch(() => alert('Network error while deleting task.'))
}

document.querySelectorAll('.task').forEach((task) => {
  task.addEventListener('dragstart', drag)
  task.addEventListener('dragend', drop)
})

document.querySelectorAll('.task-delete').forEach((button) => {
  button.addEventListener('click', (event) => {
    event.stopPropagation()
    deleteTask(button)
  })
})
