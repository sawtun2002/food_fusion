<div class="modal fade" id="recipeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 25px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h4 class="fw-bold text-coral mb-0" id="modalTitle" style="color: #ff5733;"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                <img id="modalImg" src="" class="shadow-sm mb-4" style="width: 100%; height: 350px; object-fit: cover; border-radius: 20px;">
                
                <div class="row mb-4 text-center">
                    <div class="col-4 border-end">
                        <small class="text-muted d-block italic">Cooking Time</small>
                        <span class="fw-bold" id="modalTime"></span>
                    </div>
                    <div class="col-4 border-end">
                        <small class="text-muted d-block">Cuisine</small>
                        <span class="fw-bold" id="modalCuisine"></span>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">Diet</small>
                        <span class="fw-bold" id="modalDiet"></span>
                    </div>
                </div>

                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Description</h5>
                <p id="modalDesc" class="text-muted"></p>

                <div id="extraContent">
                    <hr class="my-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-egg-fried me-2"></i>Ingredients</h5>
                    <div id="modalIngredients" class="mb-4 text-muted">Coming Soon...</div>

                    <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2"></i>Instructions</h5>
                    <div id="modalInstructions" class="text-muted">Coming Soon...</div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary rounded-pill px-5" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showRecipe(recipe) {
    // Basic Info
    document.getElementById('modalTitle').innerText = recipe.title;
    document.getElementById('modalImg').src = recipe.image_url;
    document.getElementById('modalTime').innerText = recipe.cooking_time + " mins";
    document.getElementById('modalCuisine').innerText = recipe.cuisine_type;
    document.getElementById('modalDiet').innerText = recipe.dietary_preference;
    document.getElementById('modalDesc').innerText = recipe.description;

    // Ingredients & Instructions (Handling if columns don't exist yet)
    const ingredientsContainer = document.getElementById('modalIngredients');
    const instructionsContainer = document.getElementById('modalInstructions');

    if(recipe.ingredients) {
        ingredientsContainer.innerHTML = `<ul class="ps-3">${recipe.ingredients.split('\n').map(i => `<li class='mb-1'>${i}</li>`).join('')}</ul>`;
    } else {
        ingredientsContainer.innerHTML = "<em>Chef is preparing the list... Coming soon!</em>";
    }
    
    if(recipe.instructions) {
        instructionsContainer.innerHTML = recipe.instructions.split('\n').map((step, idx) => `
            <div style="background: #fff8f6; padding: 12px; border-radius: 12px; border-left: 4px solid #ff5733; margin-bottom: 10px;">
                <strong style="color: #ff5733;">Step ${idx+1}</strong>
                <p class="mb-0 small mt-1">${step}</p>
            </div>
        `).join('');
    } else {
        instructionsContainer.innerHTML = "<em>Cooking steps are coming soon!</em>";
    }

    // Trigger Modal
    var myModal = new bootstrap.Modal(document.getElementById('recipeModal'));
    myModal.show();
}
</script>