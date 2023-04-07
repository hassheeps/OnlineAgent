/*******************
    
	Name: Brianne Coleman
	Date: March 26, 2023 
	Description: Final Project - JavaScript

*******************/

var deleteProfileButton = document.getElementById("delete");	
	deleteProfileButton.addEventListener("click", confirmDelete);

var updateProfileButton = document.getElementById("update");
	updateProfileButton.addEventListener("click", confirmUpdate);


function confirmDelete()
{
	return confirm("Are you sure you want to delete this profile?  This cannot be undone.");
	
	if(!confirm)
	{
		return false;
	}
}

function confirmDeleteImg()
{
	return confirm("Are you sure you want to delete the selected image(s)?");

	if (!confirm)
	{
		return false;
    }
}

function confirmDeleteAct()
{
	return confirm("Are you sure you want to delete the selected act?  This cannot be undone.");

	if (!confirm)
	{
		return false;
    }
}

function confirmUpdate()
{
	return confirm("Submit changes?");

	if(!confirm)
		{
		return false;
		}
}

function confirmLogOut()
{
	return confirm("Log out?");
}

function hideErrors()
{
	let error = document.getElementsByClassName("error");

	for (let i = 0; i < error.length; i++)
	{
		error[i].style.display = "none";
	}
}


function load(){

	let error = document.getEleemntsByClassName("error");
}

document.addEventListener("DOMContentLoaded", load);