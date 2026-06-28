<h3>Rapport Financier & Historique des Paiements</h3>

<div class="reports-stats">
    <div class="report-stat-item">
        <span class="report-stat-label">Revenus Générés :</span>
        <span class="report-stat-value report-stat-value--green"><?= number_format($financialStats['total_revenue'] ?? 0, 2, ',', ' ') ?> €</span>
    </div>
    
    <div class="report-stat-item">
        <span class="report-stat-label">Paiements Effectués :</span>
        <span class="report-stat-value report-stat-value--blue"><?= (int)($financialStats['total_payments'] ?? 0) ?> Transactions</span>
    </div>
</div>

<h4>Derniers encaissements</h4>
<div class="reports-table-wrapper">
    <table>
        <tr>
            <th>ID Transaction</th>
            <th>N° Réservation</th>
            <th>Numéro de place</th>
            <th>Montant</th>
            <th>Méthode</th>
            <th>Date de paiement</th>
            <th>Statut</th>
        </tr>
        <?php if(!empty($recentPayments)): ?>
            <?php foreach ($recentPayments as $pay): ?>
                <tr>
                    <td><?= htmlspecialchars($pay['transaction_id'] ?? 'N/A') ?></td>
                    <td><strong><?= htmlspecialchars($pay['reservation_number']) ?></strong></td>
                    <td><?= htmlspecialchars($pay['spot_number']) ?></td>
                    <td><strong class="amount-positive"><?= number_format($pay['amount'], 2, ',', ' ') ?> €</strong></td>
                    <td>
                        <span class="payment-method-badge">
                            <?= ucfirst(htmlspecialchars($pay['payment_method'])) ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($pay['paid_at'])) ?></td>
                    <td>
                        <?php 
                            $statusClass = 'status-' . $pay['status'];
                            $statusLabel = $pay['status'] === 'completed' ? 'Complété' : ucfirst($pay['status']);
                        ?>
                        <strong class="<?= $statusClass ?>"><?= $statusLabel ?></strong>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr class="table-empty"><td colspan="7">Aucun paiement enregistré pour le moment.</td></tr>
        <?php endif; ?>
    </table>
</div>
