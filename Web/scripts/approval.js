function Approval(opts)
{
	var options = opts;

	var elements = {
		approveButton: $('#btnApprove'),
		referenceNumber: $("#referenceNumber")
	};

	function initReservation()
	{
		elements.approveButton.click(function ()
		{
			$('<span class="spinner-border" role="status"/>').insertAfter(elements.approveButton);
			elements.approveButton.hide();
			approve(elements.referenceNumber.val());
		});
	}

	function approve(referenceNumber)
	{
		$.ajax({
			url: options.url,
			dataType: 'json',
			method: 'post',
			data: JSON.stringify({referenceNumber: referenceNumber}),
			headers: {
		        "Content-Type": "application/json",
		        "X-Csrf-Token": $('#csrf_token').val(),
		      },
			success: function (data)
			{
				if (options.returnUrl)
				{
					window.location.href = options.returnUrl;
				}
				else
				{
					window.location.reload();
				}
			}
		});
	}

	return {
		initReservation: initReservation,
		Approve: approve
	}
}
