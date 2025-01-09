let draggedTask = null
let initialPosition = null

function drag(event) {
  draggedTask = event.target
  initialPosition = event.clientY
}

function drop(event) {
  event.preventDefault()
  if (!draggedTask) return

  const deltaY = event.clientY - initialPosition
  const id = draggedTask.dataset.id
  const newStartTime = Math.max(0, deltaY / 40) * 3600 // 1 hour = 40px

  // Send updated position to the server
  fetch('reschedule_task.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, newStartTime }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) location.reload()
    })

  draggedTask = null
}
