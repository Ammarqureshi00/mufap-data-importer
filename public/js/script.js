document.addEventListener('DOMContentLoaded', function () {
      const tableBody = document.getElementById('funds-table');

      fetch('/api/mutualfunds')
            .then(response => response.json())
            .then(result => {
                  tableBody.innerHTML = ''; // clear loading text

                  const funds = result.data.data || result.data; // if paginated or not

                  if (funds.length === 0) {
                        tableBody.innerHTML = `
      <tr>
            <td colspan="14" class="text-center text-muted py-4">
                  No data found 📭
            </td>
      </tr>`;
                        return;
                  }

                  funds.forEach(fund => {
                        const row = `
<tr>
    <td>${fund.sector?.name ?? ''}</td>
    <td>${fund.amc?.name ?? ''}</td>
    <td>${fund.mutual_fund?.name ?? ''}</td>
    <td>${fund.category?.name ?? ''}</td>
    <td>${fund.inception_date ? fund.inception_date.slice(0, 10) : ''}</td>
    <td>${fund.offer ?? ''}</td>
    <td>${fund.repurchase ?? ''}</td>
    <td>${fund.nav ?? ''}</td>
    <td>${fund.validity_date ? fund.validity_date.slice(0, 10) : ''}</td>
    <td>${fund.front_end ?? ''}</td>
    <td>${fund.back_end ?? ''}</td>
    <td>${fund.contingent ?? ''}</td>
    <td>${fund.market ?? ''}</td>
    <td>${fund.trustee?.name ?? 'Null'}</td>
</tr>
`;

                        tableBody.insertAdjacentHTML('beforeend', row);
                  });
            })
            .catch(error => {
                  console.error('Error fetching data:', error);
                  tableBody.innerHTML = `
      <tr>
            <td colspan="14" class="text-center text-danger py-4">
                  Error loading data ❌
            </td>
      </tr>`;
            });
});
