<h3>Rapport Financier & Historique des Paiements</h3>

<div class="reports-stats">
    <div class="stat-card stat-card--green">
        <h4>Revenus Générés (Validés)</h4>
        <p><?= number_format($financialStats['total_revenue'] ?? 0, 2, ',', ' ') ?> €</p>
    </div>
    
    <div class="stat-card stat-card--blue">
        <h4>Paiements Effectués</h4>
        <p><?= (int)($financialStats['total_payments'] ?? 0) ?> <span>Transactions</span></p>
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
                            $color = $pay['status'] === 'completed' ? '#2ecc71' : ($pay['status'] === 'failed' ? '#e74c3c' : '#f1c40f');
                            $statusLabel = $pay['status'] === 'completed' ? 'Complété' : ucfirst($pay['status']);
                        ?>
                        <strong style="color: <?= $color ?>;"><?= $statusLabel ?></strong>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr class="table-empty"><td colspan="7">Aucun paiement enregistré pour le moment.</td></tr>
        <?php endif; ?>
    </table>
</div>
