function start() {
	uploadForm.newsTitle.disabled = true;
	uploadForm.ckNewsTitle.checked = true;
}

onload = start;

function enNewsTitle() {
	if(uploadForm.newsTitle.disabled) {
		uploadForm.newsTitle.disabled = false;
	} 
	else { 
		uploadForm.newsTitle.disabled = true; 
	}
}

function updateNewsTitle() {
	if(uploadForm.ckNewsTitle.checked) {
		uploadForm.newsTitle.value = uploadForm.title.value;
	}
}