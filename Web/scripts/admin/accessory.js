function AccessoryManagement(opts) {
	var options = opts;

	var elements = {
		activeId: $('#activeId'),
		accessoryList: $('#accessoriesTable'),

		addUnlimited: $('#chkUnlimitedAdd'),
		addQuantity: $('#addQuantity'),
		
		editName: $('#editName'),
		editUnlimited: $('#chkUnlimitedEdit'),
		editQuantity: $('#editQuantity'),
		editCredits: $('#editCredits'),
        editPeakCredits: $('#editPeakCredits'),
        editCreditApplicability: $('#editCreditApplicability'),
        editCreditsBlockedSlots: $('#editCreditsBlockedSlots'),

		accessoryDialog: $('#accessoryDialog'),
		deleteDialog: $('#deleteDialog'),
		accessoryResourcesDialog: $('#accessoryResourcesDialog'),

		addForm: $('#addForm'),
		form: $('#accessoryForm'),
		deleteForm: $('#deleteForm'),
		accessoryResourcesForm: $('#accessoryResourcesForm')
	};

	let submitAction = options.actions.add;

	var accessories = {};

	AccessoryManagement.prototype.init = function() {

		$('#add-accessory-button').click(e => {
			enableDialogAdd();
			elements.accessoryDialog.modal('show');
		});

		elements.accessoryList.delegate('.update', 'click', function(e) {
			setActiveId($(this));
			e.preventDefault();
		});

		elements.accessoryList.delegate('.edit', 'click', function() {
			editAccessory();
		});

		elements.accessoryList.delegate('.delete', 'click', function() {
			deleteAccessory();
		});

		elements.accessoryList.delegate('.resources', 'click', function() {
			showAccessoryResources();
		});

		$(".cancel").click(function() {
			$(this).closest('.dialog').modal("close");
		});
		
		elements.accessoryResourcesDialog.delegate('.resourceCheckbox', 'click', function() {
			handleAccessoryResourceClick($(this));
		});

		ConfigureAsyncForm(elements.deleteForm, getSubmitCallback(options.actions.deleteAccessory));
		ConfigureAsyncForm(elements.form, () => getSubmitCallback(submitAction)());
		ConfigureAsyncForm(elements.accessoryResourcesForm, defaultSubmitCallback);

		WireUpUnlimited(elements.addUnlimited, elements.addQuantity);
		WireUpUnlimited(elements.editUnlimited, elements.editQuantity);
	};

	var getSubmitCallback = function(action) {
		return function() {
			return options.submitUrl + "?aid=" + getActiveId() + "&action=" + action;
		};
	};

	var defaultSubmitCallback = function (form)
	{
		return options.submitUrl + "?aid=" + getActiveId() + "&action=" + form.attr('ajaxAction');
	};

	function setActiveId(activeElement) {
		var id = activeElement.closest('tr').attr('data-accessory-id');
		elements.activeId.val(id);
	}

	function getActiveId() {
		return elements.activeId.val();
	}

	function enableDialogAdd() {
		elements.accessoryDialog.find('.edit-visible').addClass('no-show');
		elements.accessoryDialog.find('.add-visible').removeClass('no-show');
		elements.accessoryDialog.find('input, select').val('');
		elements.editUnlimited.prop('checked', true);
		elements.editCreditsBlockedSlots.attr('checked', false);
		elements.editUnlimited.trigger('change');
		elements.editCreditApplicability.val('1');
		submitAction = options.actions.add;
	}

	var editAccessory = function() {
		submitAction = options.actions.edit;
		elements.accessoryDialog.find('.edit-visible').removeClass('no-show');
		elements.accessoryDialog.find('.add-visible').addClass('no-show');
		var accessory = getActiveAccessory();
		elements.editName.val(accessory.name);
		elements.editQuantity.val(accessory.quantity);

		if (accessory.quantity == '')
		{
			elements.editUnlimited.prop('checked', true);
		}
		else
		{
			elements.editUnlimited.prop('checked', false);
		}
		elements.editCredits.val(accessory.credits);
		elements.editPeakCredits.val(accessory.peakCredits);
		elements.editCreditApplicability.val(accessory.creditApplicability);
		elements.editCreditsBlockedSlots.attr('checked', accessory.editCreditsBlockedSlots == 1);

		elements.editUnlimited.trigger('change');
		elements.accessoryDialog.modal('show');
	};

	function handleAccessoryResourceClick(checkbox)
	{
		var quantities = checkbox.closest('div[resource-id]').find('.quantities');

		if (checkbox.is(':checked'))
		{
			quantities.removeClass('no-show');
		}
		else
		{
			quantities.addClass('no-show');
		}
	}

	var showAccessoryResources = function()
	{
		var accessory = getActiveAccessory();

		$.get(opts.submitUrl + '?dr=accessoryResources&aid=' + accessory.id, function(data)
		{
			elements.accessoryResourcesDialog.find(':checkbox').prop('checked', false);
			elements.accessoryResourcesDialog.find('.hidden').hide();

			$.each(data, function(idx, resource){
				var div = elements.accessoryResourcesDialog.find('[resource-id="' + resource.ResourceId + '"]');
				var checkbox = div.find(':checkbox');
				checkbox.prop('checked', true);
				handleAccessoryResourceClick(checkbox);

				div.find('[data-type="min-quantity"]').val(resource.MinQuantity);
				div.find('[data-type="max-quantity"]').val(resource.MaxQuantity);
			});
			elements.accessoryResourcesDialog.find('.resourcesDialogLabel').val(accessory.name + ' (' + accessory.quantity + ')');
			elements.accessoryResourcesDialog.modal('show');
		});
	};

	var deleteAccessory = function() {
		elements.deleteDialog.modal('show');
	};

	var getActiveAccessory = function ()
	{
		return accessories[getActiveId()];
	};

	var WireUpUnlimited = function(checkbox, quantity)
	{
		checkbox.change(function(){
			if (checkbox.is(":checked"))
			{
				quantity.val('');
				quantity.attr('disabled', 'disabled');
			}
			else
			{
				quantity.removeAttr('disabled');
			}
		});
	};

	AccessoryManagement.prototype.addAccessory = function(id, name, quantity, credits, peakCredits, creditApplicability, editCreditsBlockedSlots)
	{
		accessories[id] = {id, name, quantity, credits, peakCredits, creditApplicability, editCreditsBlockedSlots};
	};
}