function filtre(id, table_id, collonne) {
	var input, filter, table, tr, i, txtValue, j;

	input = document.getElementById(id);
	filter = input.value.toUpperCase();
	table = document.getElementById(table_id);
	tr = table.getElementsByTagName('tr');

	for (i = 1; i < tr.length; i++) {
		tr[i].style.display = 'none';
		for (j of collonne) {
			let td = tr[i].getElementsByTagName('td')[j];
			if (td) {
				text = td.textContent || td.innerText;
				if (text.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = '';
					break;
				}
			}
		}
	}
}
