// Table Management Utilities
class TableManager {
  static renderTable(containerId, data, columns, actions = null) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const table = document.createElement('div');
    table.className = 'overflow-x-auto';

    let html = '<table class="w-full text-sm">';
    
    // Header
    html += '<thead class="bg-gray-50 border-b border-gray-200">';
    html += '<tr>';
    columns.forEach(col => {
      html += `<th class="px-6 py-3 text-left font-semibold text-gray-700">${col}</th>`;
    });
    if (actions) {
      html += '<th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>';
    }
    html += '</tr>';
    html += '</thead>';

    // Body
    html += '<tbody>';
    data.forEach((row, index) => {
      html += '<tr class="border-b border-gray-200 hover:bg-gray-50 transition">';
      
      const rowValues = Array.isArray(row) ? row : Object.values(row);
      rowValues.forEach(value => {
        const displayValue = this.formatTableValue(value);
        html += `<td class="px-6 py-4 text-gray-700">${displayValue}</td>`;
      });

      if (actions) {
        html += '<td class="px-6 py-4">';
        html += this.renderActions(actions, index);
        html += '</td>';
      }

      html += '</tr>';
    });
    html += '</tbody>';
    html += '</table>';

    table.innerHTML = html;
    container.innerHTML = '';
    container.appendChild(table);
  }

  static formatTableValue(value) {
    if (!value) return '-';
    if (typeof value === 'object') return JSON.stringify(value);
    if (value.toString().match(/^\d{4}-\d{2}-\d{2}/)) {
      return App.formatDate(value);
    }
    return value;
  }

  static renderActions(actions, rowIndex) {
    let html = '<div class="flex gap-2">';
    
    if (actions.edit) {
      html += `<button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs" onclick="handleEdit(${rowIndex})">Edit</button>`;
    }

    if (actions.delete) {
      html += `<button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs" onclick="handleDelete(${rowIndex})">Delete</button>`;
    }

    html += '</div>';
    return html;
  }

  static renderCardTable(containerId, data, columns) {
    const container = document.getElementById(containerId);
    if (!container) return;

    let html = '<div class="space-y-2">';
    
    data.forEach(row => {
      html += '<div class="bg-white p-4 rounded-lg border border-gray-200 hover:border-gray-300">';
      html += '<div class="grid grid-cols-2 gap-4">';
      
      const rowValues = Object.values(row);
      columns.forEach((col, idx) => {
        if (idx < rowValues.length) {
          const value = this.formatTableValue(rowValues[idx]);
          html += `<div><p class="text-xs text-gray-600">${col}</p><p class="font-semibold text-gray-900">${value}</p></div>`;
        }
      });

      html += '</div></div>';
    });

    html += '</div>';
    container.innerHTML = html;
  }
}
