<h3>Rapport Financier & Historique des Paiements</h3>

<div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); flex: 1; min-width: 250px; border-left: 5px solid #2ecc71;">
        <h4 style="margin-top: 0; color: #7f8c8d; text-transform: uppercase; font-size: 0.9em;">Revenus Générés (Validés)</h4>
        <p style="font-size: 2.2em; font-weight: bold; color: #2c3e50; margin: 10px 0 0 0;">
            <?= number_format($financialStats['total_revenue'] ?? 0, 2, ',', ' ') ?> €
        </p>
    </div>
    
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); flex: 1; min-width: 250px; border-left: 5px solid #3498db;">
        <h4 style="margin-top: 0; color: #7f8c8d; text-transform: uppercase; font-size: 0.9em;">Paiements Effectués</h4>
        <p style="font-size: 2.2em; font-weight: bold; color: #2c3e50; margin: 10px 0 0 0;">
            <?= (int)($financialStats['total_payments'] ?? 0) ?> <span style="font-size: 0.5em; color: #95a5a6;">Transactions</span>
        </p>
    </div>
</div>

<h4>Derniers encaissements</h4>
<div style="overflow-x: auto; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
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
                    <td><strong style="color: #2ecc71;"><?= number_format($pay['amount'], 2, ',', ' ') ?> €</strong></td>
                    <td>
                        <span style="background: #ecf0f1; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">
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
            <tr><td colspan="7" style="text-align:center; padding: 20px; color: #7f8c8d;">Aucun paiement enregistré pour le moment.</td></tr>
        <?php endif; ?>
    </table>
</div>
