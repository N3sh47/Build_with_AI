<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Laundry Order</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .section { margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        .actions { margin-top: 1rem; }
        .button { padding: 0.5rem 1rem; background: #2563eb; color: #fff; border: none; border-radius: 4px; }
        .button-secondary { background: #4b5563; }
    </style>
</head>
<body>
    <h1>Create Laundry Order</h1>

    <form method="POST" action="{{ route('orders.store') }}">
        @csrf
        <div class="section">
            <label for="customer_id">Customer</label>
            <select name="customer_id" id="customer_id" required>
                <option value="">Select customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->full_name }} ({{ $customer->phone_number }})</option>
                @endforeach
            </select>
        </div>

        <div class="section">
            <label for="drop_off_date">Drop-off Date</label>
            <input type="date" name="drop_off_date" id="drop_off_date" required>

            <label for="expected_pickup_date">Expected Pickup</label>
            <input type="date" name="expected_pickup_date" id="expected_pickup_date">
        </div>

        <div class="section">
            <label for="special_instructions">Special Instructions</label>
            <textarea name="special_instructions" id="special_instructions" rows="3"></textarea>
        </div>

        <div class="section">
            <h2>Items & Services</h2>
            <table id="items-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Garment Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="items[0][service_name]" required></td>
                        <td><input type="text" name="items[0][garment_description]"></td>
                        <td><input type="number" name="items[0][quantity]" min="1" value="1" required></td>
                        <td><input type="number" name="items[0][unit_price]" min="0" step="0.01" required></td>
                        <td><button type="button" class="button button-secondary" onclick="removeRow(this)">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <div class="actions">
                <button type="button" class="button button-secondary" onclick="addRow()">Add Item</button>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="button">Save Order</button>
        </div>
    </form>

    <script>
        let rowIndex = 1;

        function addRow() {
            const table = document.getElementById('items-table').querySelector('tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="items[${rowIndex}][service_name]" required></td>
                <td><input type="text" name="items[${rowIndex}][garment_description]"></td>
                <td><input type="number" name="items[${rowIndex}][quantity]" min="1" value="1" required></td>
                <td><input type="number" name="items[${rowIndex}][unit_price]" min="0" step="0.01" required></td>
                <td><button type="button" class="button button-secondary" onclick="removeRow(this)">Remove</button></td>
            `;
            table.appendChild(row);
            rowIndex += 1;
        }

        function removeRow(button) {
            const row = button.closest('tr');
            const table = document.getElementById('items-table').querySelector('tbody');

            if (table.rows.length > 1) {
                row.remove();
            }
        }
    </script>
</body>
</html>
