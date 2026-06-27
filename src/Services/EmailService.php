<?php
namespace App\Services;

class EmailService {
    private $apiKey;
    private $fromEmail;
    private $supportEmail;
    private $websiteUrl;

    public function __construct() {
        // On récupère les valeurs depuis le fichier .env
        $this->apiKey = $_ENV['RESEND_API_KEY'] ?? ''; 
        $this->fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? 'onboarding@resend.dev';
        
        // Nouvelles variables pour le support et le site (à ajouter dans ton .env)
        $this->supportEmail = $_ENV['SUPPORT_EMAIL'] ?? 'contact@reserveviteuneplace.arthur-drugeault.fr';
        $this->websiteUrl = $_ENV['APP_URL'] ?? 'https://reserveviteuneplace.arthur-drugeault.fr';
    }

    /**
     * Template global pour unifier tous les emails
     */
    private function getEmailTemplate($title, $content) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; color: #333333;'>
            <!-- En-tête -->
            <div style='text-align: center; border-bottom: 2px solid #3498db; padding-bottom: 15px; margin-bottom: 20px;'>
                <h2 style='color: #2c3e50; margin: 0;'>{$title}</h2>
            </div>
            
            <!-- Contenu principal -->
            <div style='font-size: 15px; line-height: 1.6;'>
                {$content}
            </div>
            
            <!-- Pied de page avec contact et lien -->
            <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; font-size: 13px; color: #666666; text-align: center;'>
                <p style='margin: 5px 0;'>Une question ou un problème ? <br>
                <a href='mailto:{$this->supportEmail}' style='color: #3498db; text-decoration: none; font-weight: bold;'>Contactez notre support</a></p>
                
                <p style='margin: 10px 0;'>Accédez à votre espace sur : <br>
                <a href='{$this->websiteUrl}' style='color: #3498db; text-decoration: none;'>{$this->websiteUrl}</a></p>
                
                <p style='margin: 20px 0 0 0; font-size: 11px; color: #999999;'>
                    Ceci est un email automatique, merci de ne pas y répondre directement.
                </p>
            </div>
        </div>
        ";
    }

    private function send($to, $subject, $htmlContent) {
        $ch = curl_init('https://api.resend.com/emails');
        $payload = json_encode([
            'from' => 'Parking Admin <' . $this->fromEmail . '>',
            'to' => [$to],
            'subject' => $subject,
            'html' => $htmlContent
        ]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        
        // --- NOUVEAU CODE POUR LE DEBUGGING ---
        if (curl_errno($ch)) {
            error_log('Erreur cURL : ' . curl_error($ch));
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 400) {
                error_log("Erreur Resend (HTTP $httpCode) : " . $response);
            }
        }
        // --------------------------------------

        curl_close($ch);
        return $response;
    }

    public function sendVerificationCode($to, $code) {
        $subject = "Votre code de vérification Parking";
        $content = "
            <p>Bonjour,</p>
            <p>Pour activer votre compte, veuillez utiliser le code ci-dessous :</p>
            <div style='text-align: center; margin: 25px 0;'>
                <span style='font-size: 28px; font-weight: bold; color: #3498db; letter-spacing: 5px; background: #f4f6f9; padding: 15px 25px; border-radius: 8px;'>{$code}</span>
            </div>
            <p style='text-align: center; color: #e74c3c; font-size: 14px;'>Ce code expire dans 15 minutes.</p>
        ";
        
        $html = $this->getEmailTemplate("Bienvenue sur notre application !", $content);
        $this->send($to, $subject, $html);
    }

    public function sendReservationConfirmation($to, $details) {
        $subject = "Confirmation de votre réservation - Place " . $details['spot_number'];
        $content = "
            <p>Merci pour votre réservation. Voici les détails et la facturation :</p>
            
            <table style='width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;'>
                <tr><td style='padding: 12px; border: 1px solid #ddd;'><strong>N° Réservation</strong></td><td style='padding: 12px; border: 1px solid #ddd; color: #3498db;'><strong>{$details['reservation_number']}</strong></td></tr>
                <tr style='background: #f4f6f9;'><td style='padding: 12px; border: 1px solid #ddd;'><strong>Place N°</strong></td><td style='padding: 12px; border: 1px solid #ddd;'>{$details['spot_number']}</td></tr>
                <tr><td style='padding: 12px; border: 1px solid #ddd;'><strong>Arrivée</strong></td><td style='padding: 12px; border: 1px solid #ddd;'>{$details['start_time']}</td></tr>
                <tr style='background: #f4f6f9;'><td style='padding: 12px; border: 1px solid #ddd;'><strong>Départ</strong></td><td style='padding: 12px; border: 1px solid #ddd;'>{$details['end_time']}</td></tr>
                <tr><td style='padding: 12px; border: 1px solid #ddd;'><strong>Prix Total TTC</strong></td><td style='padding: 12px; border: 1px solid #ddd;'><strong>{$details['total_price']} €</strong></td></tr>
                <tr style='background: #f4f6f9;'><td style='padding: 12px; border: 1px solid #ddd;'><strong>Moyen de paiement</strong></td><td style='padding: 12px; border: 1px solid #ddd;'>Carte Bancaire (Payé)</td></tr>
                <tr><td style='padding: 12px; border: 1px solid #ddd;'><strong>ID Transaction</strong></td><td style='padding: 12px; border: 1px solid #ddd;'>{$details['transaction_id']}</td></tr>
            </table>
            
            <p style='margin-top: 20px;'>Vous pouvez modifier ou annuler votre réservation depuis votre espace personnel.</p>
        ";
        
        $html = $this->getEmailTemplate("✅ Réservation Confirmée", $content);
        $this->send($to, $subject, $html);
    }

    public function sendReservationUpdate($to, $details) {
        $subject = "Modification de votre réservation - Place " . $details['spot_number'];
        $content = "
            <p>Votre réservation a bien été mise à jour. Voici vos nouvelles dates :</p>
            
            <ul style='background: #f4f6f9; padding: 20px 20px 20px 40px; border-radius: 5px; border: 1px solid #ddd;'>
                <li style='margin-bottom: 10px;'><strong>Place :</strong> {$details['spot_number']}</li>
                <li style='margin-bottom: 10px;'><strong>Nouvelle Arrivée :</strong> {$details['start_time']}</li>
                <li style='margin-bottom: 10px;'><strong>Nouveau Départ :</strong> {$details['end_time']}</li>
                <li><strong>Nouveau Prix Total :</strong> {$details['total_price']} €</li>
            </ul>
        ";
        
        $html = $this->getEmailTemplate("🔄 Réservation Modifiée", $content);
        $this->send($to, $subject, $html);
    }

    public function sendLoginCode($to, $code) {
        $subject = "Votre code de connexion - Parking";
        $content = "
            <p>Bonjour,</p>
            <p>Une tentative de connexion a été détectée. Utilisez le code suivant pour y accéder :</p>
            <div style='text-align: center; margin: 25px 0;'>
                <span style='font-size: 28px; font-weight: bold; color: #3498db; letter-spacing: 5px; background: #f4f6f9; padding: 15px 25px; border-radius: 8px;'>{$code}</span>
            </div>
            <p style='text-align: center; color: #e74c3c; font-size: 14px;'>Ce code expire dans 15 minutes.</p>
            <p style='font-size: 13px; color: #666;'><em>S'il ne s'agit pas de vous, vous pouvez ignorer cet email en toute sécurité.</em></p>
        ";
        
        $html = $this->getEmailTemplate("🔐 Sécurité de votre compte", $content);
        $this->send($to, $subject, $html);
    }


    public function sendReservationCancellation($to, $details) {
        $subject = "Annulation de votre réservation - Place " . $details['spot_number'];
        $content = "
            <p>Votre réservation a bien été annulée.</p>
            
            <ul style='background: #f4f6f9; padding: 20px 20px 20px 40px; border-radius: 5px; border: 1px solid #ddd;'>
                <li style='margin-bottom: 10px;'><strong>Place :</strong> {$details['spot_number']}</li>
                <li style='margin-bottom: 10px;'><strong>Arrivée initiale :</strong> {$details['start_time']}</li>
                <li style='margin-bottom: 10px;'><strong>Départ initial :</strong> {$details['end_time']}</li>
            </ul>
            
            <p style='margin-top: 20px;'>Si vous avez déjà été débité, le remboursement sera traité automatiquement sur votre moyen de paiement selon nos conditions.</p>
        ";
        
        $html = $this->getEmailTemplate("❌ Réservation Annulée", $content);
        $this->send($to, $subject, $html);
    }
}